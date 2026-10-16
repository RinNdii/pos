<?php
namespace App\Controllers;
use App\Models\ReceivingModel;
use App\Models\PurchaseModel;
use App\Models\PurchaseDetailModel;
use App\Models\ItemModel;
use App\Models\RawMaterialModel;

class Receivings extends BaseController {
    public function index(string $view = 'pending') {
        $db = \Config\Database::connect();
        $view = $view === 'warehouse' ? 'warehouse' : 'pending';
        
        // Data PO yang belum diterima (Dipesan)
        $data['pending_pos'] = $db->table('purchases')
            ->select('purchases.*, suppliers.name as supplier_name, GROUP_CONCAT(DISTINCT COALESCE(items.name, raw_materials.name, purchase_details.product_name) SEPARATOR ", ") as product_names')
            ->join('suppliers', 'suppliers.id = purchases.supplier_id', 'left')
            ->join('purchase_details', 'purchase_details.purchase_id = purchases.id', 'left')
            ->join('items', 'items.id = purchase_details.item_id', 'left')
            ->join('raw_materials', 'raw_materials.id = purchase_details.raw_material_id', 'left')
            ->where('purchases.status', 'Pending')
            ->groupBy('purchases.id, purchases.po_number, purchases.supplier_id, purchases.po_date, purchases.total_items, purchases.status, suppliers.name')
            ->orderBy('po_date', 'ASC')
            ->get()->getResultArray();

        // Data Riwayat Penerimaan, digabung berdasarkan barang yang sama.
        $receivedDetails = $db->table('receivings')
            ->select('receivings.id as receiving_id, receivings.purchase_id, receivings.receive_number, receivings.receive_date,
                purchase_details.item_id, purchase_details.raw_material_id,
                purchase_details.product_name, purchase_details.unit, purchase_details.qty,
                items.name as item_name, items.unit as item_unit, items.stock as item_stock,
                raw_materials.name as raw_material_name, raw_materials.unit as raw_material_unit,
                raw_materials.stock as raw_material_stock, items.stock_updated_at as item_stock_updated_at,
                raw_materials.stock_updated_at as raw_material_stock_updated_at,
                custom_raw.stock as custom_raw_stock, custom_raw.stock_updated_at as custom_raw_stock_updated_at')
            ->join('purchase_details', 'purchase_details.purchase_id = receivings.purchase_id')
            ->join('items', 'items.id = purchase_details.item_id', 'left')
            ->join('raw_materials', 'raw_materials.id = purchase_details.raw_material_id', 'left')
            ->join('raw_materials custom_raw', 'custom_raw.name = purchase_details.product_name AND custom_raw.unit = purchase_details.unit', 'left')
            ->orderBy('receivings.receive_date', 'DESC')
            ->orderBy('receivings.id', 'DESC')
            ->get()->getResultArray();

        $receivings = [];
        foreach ($receivedDetails as $detail) {
            if (!empty($detail['item_id'])) {
                $key = 'item:' . $detail['item_id'];
                $name = $detail['item_name'];
                $unit = $detail['item_unit'];
                $currentStock = (int) $detail['item_stock'];
                $stockType = 'item';
                $stockId = $detail['item_id'];
                $stockUpdatedAt = $detail['item_stock_updated_at'] ?: $detail['receive_date'];
            } elseif (!empty($detail['raw_material_id'])) {
                $key = 'raw_material:' . $detail['raw_material_id'];
                $name = $detail['raw_material_name'];
                $unit = $detail['raw_material_unit'];
                $currentStock = (int) $detail['raw_material_stock'];
                $stockType = 'raw_material';
                $stockId = $detail['raw_material_id'];
                $stockUpdatedAt = $detail['raw_material_stock_updated_at'] ?: $detail['receive_date'];
            } else {
                $name = $detail['product_name'];
                $unit = $detail['unit'];
                $customKey = md5(strtolower(trim($name) . '|' . trim($unit)));
                $key = 'custom:' . $customKey;
                $currentStock = $detail['custom_raw_stock'] !== null
                    ? (int) $detail['custom_raw_stock']
                    : null;
                $stockType = 'custom';
                $stockId = $customKey;
                $stockUpdatedAt = $detail['custom_raw_stock_updated_at'] ?: $detail['receive_date'];
            }

            if (trim((string) $name) === '' || trim((string) $unit) === '') {
                continue;
            }

            $isNewProduct = !isset($receivings[$key]);
            if ($isNewProduct) {
                $receivings[$key] = [
                    'receiving_id' => $detail['receiving_id'],
                    'receive_number' => $detail['receive_number'],
                    'receive_date' => $detail['receive_date'],
                    'stock_updated_at' => $stockUpdatedAt,
                    'product_name' => $name,
                    'unit' => $unit,
                    'quantity' => (int) $detail['qty'],
                    'stock' => $currentStock ?? 0,
                    'stock_type' => $stockType,
                    'stock_id' => $stockId,
                ];
            }

            if ($currentStock === null) {
                $receivings[$key]['stock'] += (int) $detail['qty'];
            }

            if (!$isNewProduct && $receivings[$key]['receiving_id'] === $detail['receiving_id']) {
                $receivings[$key]['quantity'] += (int) $detail['qty'];
            }
        }

        $data['receivings'] = array_values($receivings);
        $data['warehouse_materials'] = array_values(array_filter($receivings, static function (array $stockItem): bool {
            return in_array($stockItem['stock_type'], ['raw_material', 'custom'], true);
        }));
        $data['warehouse_products'] = array_values(array_filter($receivings, static function (array $stockItem): bool {
            return $stockItem['stock_type'] === 'item';
        }));
        $data['view_mode'] = $view;

        return view('receivings/index', $data);
    }

    public function warehouseDetail(string $type, string $id)
    {
        if (!in_array($type, ['item', 'raw_material', 'custom'], true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $db = \Config\Database::connect();

        if ($type === 'custom') {
            $customRows = $db->table('purchase_details')
                ->select('purchase_details.product_name as name, purchase_details.unit, SUM(purchase_details.qty) as total_quantity, custom_raw.stock as current_stock')
                ->join('receivings', 'receivings.purchase_id = purchase_details.purchase_id', 'left')
                ->join('raw_materials custom_raw', 'custom_raw.name = purchase_details.product_name AND custom_raw.unit = purchase_details.unit', 'left')
                ->where('purchase_details.item_id IS NULL', null, false)
                ->where('purchase_details.raw_material_id IS NULL', null, false)
                ->where('purchase_details.product_name IS NOT NULL', null, false)
                ->groupBy('purchase_details.product_name, purchase_details.unit')
                ->get()->getResultArray();

            $customItem = null;
            foreach ($customRows as $row) {
                if (md5(strtolower(trim($row['name']) . '|' . trim($row['unit']))) === $id) {
                    $customItem = $row;
                    break;
                }
            }

            if (!$customItem) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Barang custom tidak ditemukan.');
            }

            $data['stock_item'] = [
                'name' => $customItem['name'],
                'unit' => $customItem['unit'],
                'stock' => $customItem['current_stock'] !== null
                    ? (int) $customItem['current_stock']
                    : (int) $customItem['total_quantity'],
            ];
            $data['receiving_history'] = $db->table('purchase_details')
                ->select('receivings.receive_number, receivings.receive_date, purchase_details.purchase_price, SUM(purchase_details.qty) as quantity')
                ->join('receivings', 'receivings.purchase_id = purchase_details.purchase_id')
                ->where('purchase_details.item_id IS NULL', null, false)
                ->where('purchase_details.raw_material_id IS NULL', null, false)
                ->where('purchase_details.product_name', $customItem['name'])
                ->where('purchase_details.unit', $customItem['unit'])
                ->groupBy('receivings.id, receivings.receive_number, receivings.receive_date, purchase_details.purchase_price')
                ->orderBy('receivings.receive_date', 'DESC')
                ->orderBy('receivings.id', 'DESC')
                ->get()->getResultArray();
            $data['sales_history'] = $db->table('sale_material_report_details')
                ->select('sale_material_reports.item_name, sale_material_reports.created_at as report_date, sale_material_report_details.qty_used as qty')
                ->join('sale_material_reports', 'sale_material_reports.id = sale_material_report_details.report_id')
                ->where('sale_material_report_details.raw_material_name', $customItem['name'])
                ->where('sale_material_report_details.raw_material_unit', $customItem['unit'])
                ->orderBy('sale_material_reports.created_at', 'DESC')
                ->get()->getResultArray();
            $data['outgoing_is_material_report'] = true;

            return view('receivings/warehouse_detail', $data);
        }

        $idColumn = $type === 'item' ? 'item_id' : 'raw_material_id';
        $masterTable = $type === 'item' ? 'items' : 'raw_materials';

        $stockItem = $db->table($masterTable)
            ->where('id', (int) $id)
            ->get()->getRowArray();

        if (!$stockItem) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Barang tidak ditemukan.');
        }

        $data['stock_item'] = [
            'name' => $stockItem['name'],
            'unit' => $stockItem['unit'],
            'stock' => $stockItem['stock'],
        ];
        $data['receiving_history'] = $db->table('receivings')
            ->select('receivings.receive_number, receivings.receive_date, purchase_details.purchase_price, SUM(purchase_details.qty) as quantity')
            ->join('purchase_details', 'purchase_details.purchase_id = receivings.purchase_id')
            ->where('purchase_details.' . $idColumn, (int) $id)
            ->groupBy('receivings.id, receivings.receive_number, receivings.receive_date, purchase_details.purchase_price')
            ->orderBy('receivings.receive_date', 'DESC')
            ->orderBy('receivings.id', 'DESC')
            ->get()->getResultArray();
        if ($type === 'raw_material') {
            $data['sales_history'] = $db->table('sale_material_report_details')
                ->select('sale_material_reports.item_name, sale_material_reports.created_at as report_date, sale_material_report_details.qty_used as qty')
                ->join('sale_material_reports', 'sale_material_reports.id = sale_material_report_details.report_id')
                ->where('sale_material_report_details.raw_material_id', (int) $id)
                ->orderBy('sale_material_reports.created_at', 'DESC')
                ->get()->getResultArray();
            $data['outgoing_is_material_report'] = true;
        } else {
            $data['sales_history'] = $db->table('sale_details')
                ->select('sales.invoice_number, sales.sale_date, sale_details.qty, sale_details.price, sale_details.subtotal')
                ->join('sales', 'sales.id = sale_details.sale_id')
                ->where('sale_details.item_id', (int) $id)
                ->orderBy('sales.sale_date', 'DESC')
                ->get()->getResultArray();
            $data['outgoing_is_material_report'] = false;
        }

        return view('receivings/warehouse_detail', $data);
    }

    public function detail($purchase_id) {
        $db = \Config\Database::connect();

        $purchase = $db->table('purchases')
            ->select('purchases.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchases.supplier_id', 'left')
            ->where('purchases.id', $purchase_id)
            ->where('purchases.status', 'Pending')
            ->get()
            ->getRowArray();

        if (!$purchase) {
            return redirect()->to('/receivings')->with('error', 'PO tidak ditemukan atau sudah diterima.');
        }

        $details = $db->table('purchase_details')
            ->select('purchase_details.*, items.name as item_name, items.unit as item_unit,
                raw_materials.name as raw_material_name, raw_materials.unit as raw_material_unit')
            ->join('items', 'items.id = purchase_details.item_id', 'left')
            ->join('raw_materials', 'raw_materials.id = purchase_details.raw_material_id', 'left')
            ->where('purchase_details.purchase_id', $purchase_id)
            ->get()
            ->getResultArray();

        foreach ($details as &$detail) {
            if (!empty($detail['item_id'])) {
                $detail['display_name'] = $detail['item_name'];
                $detail['display_unit'] = $detail['item_unit'];
            } elseif (!empty($detail['raw_material_id'])) {
                $detail['display_name'] = $detail['raw_material_name'];
                $detail['display_unit'] = $detail['raw_material_unit'];
            } else {
                $detail['display_name'] = $detail['product_name'];
                $detail['display_unit'] = $detail['unit'];
            }

            $detail['line_total'] = (float) ($detail['purchase_price'] ?? 0) * (int) $detail['qty'];
        }
        unset($detail);

        $totalPurchase = array_sum(array_column($details, 'line_total'));
        $productNames = array_map(function ($detail) {
            return $detail['display_name'];
        }, $details);

        return view('receivings/detail', [
            'purchase' => $purchase,
            'details' => $details,
            'total_purchase' => $totalPurchase,
            'product_names' => implode(', ', $productNames),
        ]);
    }

    public function process($purchase_id) {
        $db = \Config\Database::connect();
        $purchaseModel = new PurchaseModel();
        $detailModel   = new PurchaseDetailModel();
        $itemModel     = new ItemModel();
        $rawMaterialModel = new RawMaterialModel();
        $receivingModel= new ReceivingModel();

        // Mulai Transaksi Database (agar jika error di tengah jalan, dibatalkan semua)
        $db->transStart();

        // 1. Update status PO menjadi Diterima
        $purchaseModel->update($purchase_id, ['status' => 'Diterima']);

        // 2. Catat riwayat Penerimaan
        $receiveData = [
            'receive_number' => 'RCV-' . date('Ymd') . '-' . rand(100, 999),
            'purchase_id'    => $purchase_id,
            'receive_date'   => date('Y-m-d')
        ];
        $receivingModel->insert($receiveData);

        // 3. Tambahkan Stok ke Master Barang
        $details = $detailModel->where('purchase_id', $purchase_id)->findAll();
        foreach ($details as $row) {
            if (!empty($row['item_id'])) {
                $item = $itemModel->find($row['item_id']);
                if ($item) {
                    $itemModel->update($item['id'], [
                        'stock' => $item['stock'] + $row['qty'],
                        'stock_updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            } elseif (!empty($row['raw_material_id'])) {
                $rawMaterial = $rawMaterialModel->find($row['raw_material_id']);
                if ($rawMaterial) {
                    $rawMaterialModel->update($rawMaterial['id'], [
                        'stock' => $rawMaterial['stock'] + $row['qty'],
                        'stock_updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            } else {
                $rawMaterial = $rawMaterialModel
                    ->where('name', $row['product_name'])
                    ->where('unit', $row['unit'])
                    ->first();

                if ($rawMaterial) {
                    $rawMaterialModel->update($rawMaterial['id'], [
                        'stock' => $rawMaterial['stock'] + $row['qty'],
                        'stock_updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $rawMaterialModel->insert([
                        'sku' => 'cust-' . time() . '-' . $row['id'],
                        'name' => $row['product_name'],
                        'unit' => $row['unit'],
                        'stock' => $row['qty'],
                        'stock_updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->to('/receivings')->with('error', 'Gagal memproses penerimaan barang.');
        }

        return redirect()->to('/receivings')->with('success', 'Barang berhasil diterima dan stok telah bertambah.');
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $purchaseModel = new PurchaseModel();
        $detailModel = new PurchaseDetailModel();
        $purchase = $purchaseModel->where('id', $id)->where('status', 'Pending')->first();

        if (!$purchase) {
            return redirect()->to('/receivings')->with('error', 'PO tidak ditemukan atau sudah diterima.');
        }

        $db->transStart();
        $detailModel->where('purchase_id', $id)->delete();
        $purchaseModel->delete($id);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/receivings')->with('error', 'Gagal menghapus PO.');
        }

        return redirect()->to('/receivings')->with('success', 'PO berhasil dihapus.');
    }

    public function deleteWarehouse(string $type, string $id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        if ($type === 'item') {
            (new ItemModel())->delete((int) $id);
        } elseif ($type === 'raw_material') {
            (new RawMaterialModel())->delete((int) $id);
        } elseif ($type === 'custom') {
            $rows = $db->table('purchase_details')
                ->select('product_name, unit')
                ->where('raw_material_id IS NULL', null, false)
                ->get()->getResultArray();

            foreach ($rows as $row) {
                if (md5(strtolower(trim($row['product_name']) . '|' . trim($row['unit']))) === $id) {
                    $db->table('purchase_details')
                        ->where('raw_material_id IS NULL', null, false)
                        ->where('product_name', $row['product_name'])
                        ->where('unit', $row['unit'])
                        ->delete();
                    $db->table('raw_materials')
                        ->where('name', $row['product_name'])
                        ->where('unit', $row['unit'])
                        ->delete();
                    break;
                }
            }
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/warehouse')->with('error', 'Barang gagal dihapus dari gudang. Data masih digunakan oleh transaksi lain.');
        }

        return redirect()->to('/warehouse')->with('success', 'Barang berhasil dihapus dari gudang.');
    }

    public function reduceWarehouse(string $type, string $id)
    {
        $quantity = (float) ($this->request->getPost('quantity') ?: 1);
        if ($quantity <= 0) {
            return redirect()->to('/warehouse')->with('error', 'Jumlah pengurangan stok harus lebih dari 0.');
        }

        $rawMaterialModel = new RawMaterialModel();
        $stockItem = null;

        if ($type === 'item') {
            $stockItem = (new ItemModel())->find((int) $id);
        } elseif ($type === 'raw_material') {
            $stockItem = $rawMaterialModel->find((int) $id);
        } elseif ($type === 'custom') {
            $customRows = $rawMaterialModel->findAll();
            foreach ($customRows as $row) {
                if (md5(strtolower(trim($row['name']) . '|' . trim($row['unit']))) === $id) {
                    $stockItem = $row;
                    break;
                }
            }
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (!$stockItem) {
            return redirect()->to('/warehouse')->with('error', 'Barang tidak ditemukan.');
        }

        $currentStock = (float) ($stockItem['stock'] ?? 0);
        if ($quantity > $currentStock) {
            return redirect()->to('/warehouse')->with('error', 'Jumlah pengurangan melebihi stok yang tersedia.');
        }

        $model = $type === 'item' ? new ItemModel() : $rawMaterialModel;
        $model->update((int) $stockItem['id'], [
            'stock' => $currentStock - $quantity,
            'stock_updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/warehouse')->with('success', 'Stok berhasil dikurangi.');
    }
}