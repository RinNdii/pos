<?php
namespace App\Controllers;
use App\Models\ItemModel;
use App\Models\CustomerModel;
use App\Models\SaleModel;
use App\Models\SaleDetailModel;

class Pos extends BaseController {
    public function index() {
        $itemModel = new ItemModel();
        $customerModel = new CustomerModel();

        // Hanya tampilkan barang yang stoknya lebih dari 0
        $data['items'] = $itemModel->where('stock >', 0)->findAll();
        $data['customers'] = $customerModel->findAll();
        $data['invoice_number'] = 'INV-' . date('YmdHis');

        return view('pos/index', $data);
    }

    public function checkout() {
        $db = \Config\Database::connect();
        $saleModel = new SaleModel();
        $detailModel = new SaleDetailModel();
        $itemModel = new ItemModel();

        $items = $this->request->getPost('item_id') ?? [];
        $qtys = $this->request->getPost('qty') ?? [];
        $prices = $this->request->getPost('price') ?? [];
        $discountPercent = min(100, max(0, (float) ($this->request->getPost('discount') ?? 0)));
        $paymentMethod = $this->request->getPost('payment_method');
        $transferBank = $this->request->getPost('transfer_bank');
        $validPaymentMethods = ['Tunai', 'QRIS', 'Transfer'];
        
        if (empty($items)) {
            return redirect()->to('/pos')->with('error', 'Keranjang kosong!');
        }

        if (!in_array($paymentMethod, $validPaymentMethods, true)) {
            return redirect()->to('/pos')->with('error', 'Metode pembayaran tidak valid.');
        }

        $validTransferBanks = [
            'BRI (Bank Rakyat Indonesia)', 'Bank Mandiri', 'BNI (Bank Negara Indonesia)',
            'BTN (Bank Tabungan Negara)', 'BCA (Bank Central Asia)', 'CIMB Niaga',
            'Bank Danamon', 'PermataBank', 'Panin Bank', 'Maybank Indonesia',
            'BSI (Bank Syariah Indonesia)', 'Bank Muamalat', 'BCA Syariah',
        ];

        if ($paymentMethod === 'Transfer' && !in_array($transferBank, $validTransferBanks, true)) {
            return redirect()->to('/pos')->with('error', 'Pilih bank tujuan transfer.');
        }

        $db->transStart();

        // 1. Simpan Transaksi Utama
        $totalAmount = 0;
        $saleId = $saleModel->insert([
            'invoice_number' => $this->request->getPost('invoice_number'),
            'customer_id'    => $this->request->getPost('customer_id') ?: null,
            'sale_date'      => date('Y-m-d H:i:s'),
            'payment_method' => $paymentMethod,
            'transfer_bank'  => $paymentMethod === 'Transfer' ? $transferBank : null,
            'discount'       => 0,
            'total_amount'   => 0 // Diupdate setelah looping detail
        ]);

        // 2. Simpan Detail & Kurangi Stok
        foreach ($items as $key => $itemId) {
            $qty = (int) ($qtys[$key] ?? 0);
            $currentItem = $itemModel->find($itemId);

            if ($qty <= 0 || !$currentItem || $qty > (int) $currentItem['stock']) {
                $db->transRollback();
                return redirect()->to('/pos')->with('error', 'Jumlah barang melebihi stok atau barang tidak ditemukan.');
            }

            // Harga diambil dari master barang agar tidak dapat diubah dari browser.
            $price = (float) $currentItem['selling_price'];
            $subtotal = $qty * $price;
            $totalAmount += $subtotal;

            $detailModel->insert([
                'sale_id'  => $saleId,
                'item_id'  => $itemId,
                'qty'      => $qty,
                'price'    => $price,
                'subtotal' => $subtotal
            ]);

            // Kurangi stok barang
            $itemModel->update($itemId, [
                'stock' => $currentItem['stock'] - $qty,
                'stock_updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // 3. Update Total Harga dan diskon aktual yang digunakan
        $appliedDiscount = $totalAmount * $discountPercent / 100;
        $saleModel->update($saleId, [
            'discount'     => $appliedDiscount,
            'total_amount' => $totalAmount - $appliedDiscount,
        ]);

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->to('/pos')->with('error', 'Transaksi gagal diproses.');
        }

        return redirect()->to('/pos')->with('success', 'Pembayaran berhasil. Invoice masuk ke riwayat penjualan.');
    }

    public function history() {
        $db = \Config\Database::connect();
        
        // Ambil data penjualan di-join dengan nama customer
        $data['sales'] = $db->table('sales')
            ->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->orderBy('sale_date', 'DESC')
            ->get()->getResultArray();

        return view('pos/history', $data);
    }

    public function detail($id) {
        $db = \Config\Database::connect();

        $sale = $db->table('sales')
            ->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->where('sales.id', $id)
            ->get()->getRowArray();

        if (!$sale) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Transaksi tidak ditemukan.');
        }

        $rawMaterialModel = new \App\Models\RawMaterialModel();
        $data['sale'] = $sale;
        $data['details'] = $db->table('sale_details')
            ->select('sale_details.*, items.name as item_name, items.unit')
            ->join('items', 'items.id = sale_details.item_id', 'left')
            ->where('sale_details.sale_id', $id)
            ->get()->getResultArray();
        $data['material_reports'] = $db->table('sale_material_report_details')
            ->select('sale_material_report_details.raw_material_name, sale_material_report_details.raw_material_unit, sale_material_report_details.qty_used')
            ->join('sale_material_reports', 'sale_material_reports.id = sale_material_report_details.report_id')
            ->where('sale_material_reports.sale_id', $id)
            ->orderBy('sale_material_report_details.id', 'ASC')
            ->get()->getResultArray();
        $data['raw_materials'] = $rawMaterialModel->orderBy('name', 'ASC')->findAll();

        return view('pos/detail', $data);
    }

    public function saveReport($saleId)
    {
        $db = \Config\Database::connect();
        $rawMaterialModel = new \App\Models\RawMaterialModel();

        $sale = $db->table('sales')->where('id', $saleId)->get()->getRowArray();
        if (!$sale) {
            return redirect()->to('/pos/history')->with('error', 'Transaksi tidak ditemukan.');
        }

        $existingReport = $db->table('sale_material_reports')->where('sale_id', $saleId)->countAllResults();
        if ($existingReport > 0) {
            return redirect()->to('/pos/history')->with('info', 'Laporan bahan baku untuk transaksi ini sudah selesai dan tidak bisa diedit.');
        }

        $itemIds = $this->request->getPost('item_id') ?? [];
        $rawMaterialIds = $this->request->getPost('raw_id') ?? [];
        $qtyUsed = $this->request->getPost('raw_qty') ?? [];

        if (empty($itemIds) || empty($rawMaterialIds) || empty($qtyUsed)) {
            return redirect()->to('/pos/history')->with('error', 'Pilih bahan baku dan qty yang digunakan untuk laporan.');
        }

        $reportInsert = $db->table('sale_material_reports')->insert([
            'sale_id' => $saleId,
            'item_id' => (int) ($itemIds[0] ?? 0),
            'item_name' => $this->request->getPost('item_name')[0] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if (!$reportInsert) {
            return redirect()->to('/pos/history')->with('error', 'Gagal menyimpan laporan bahan baku.');
        }

        $reportId = $db->insertID();

        foreach ($rawMaterialIds as $index => $rawMaterialId) {
            if (empty($rawMaterialId)) {
                continue;
            }

            $used = (float) ($qtyUsed[$index] ?? 0);
            if ($used <= 0) {
                continue;
            }

            $selectedValue = trim((string) $rawMaterialId);
            $customValue = null;
            if (str_starts_with($selectedValue, 'custom:')) {
                $customValue = urldecode(substr($selectedValue, 7));
                [$customName, $customUnit] = array_pad(explode('|', $customValue, 2), 2, '');
                $customName = trim((string) $customName);
                $customUnit = trim((string) $customUnit ?: 'pcs');

                $rawMaterial = $db->table('raw_materials')
                    ->where('name', $customName)
                    ->where('unit', $customUnit)
                    ->get()->getRowArray();

                if (!$rawMaterial) {
                    $db->table('raw_materials')->insert([
                        'sku' => 'MAN-' . strtoupper(substr(md5($customName . '|' . $customUnit), 0, 8)),
                        'name' => $customName,
                        'unit' => $customUnit,
                        'stock' => 0,
                    ]);
                    $rawMaterial = $db->table('raw_materials')
                        ->where('name', $customName)
                        ->where('unit', $customUnit)
                        ->get()->getRowArray();
                }

                if (!$rawMaterial) {
                    return redirect()->to('/pos/history')->with('error', 'Data bahan custom tidak dapat dibuat.');
                }

                if ((float) ($rawMaterial['stock'] ?? 0) < $used) {
                    return redirect()->to('/pos/history')->with('error', 'Stok bahan baku ' . $rawMaterial['name'] . ' tidak cukup untuk dipakai.');
                }

                $db->table('sale_material_report_details')->insert([
                    'report_id' => $reportId,
                    'raw_material_id' => (int) $rawMaterial['id'],
                    'raw_material_name' => $rawMaterial['name'],
                    'raw_material_unit' => $rawMaterial['unit'],
                    'qty_used' => $used,
                ]);

                $db->table('raw_materials')->where('id', $rawMaterial['id'])
                    ->set('stock', 'stock - ' . $used, false)
                    ->set('stock_updated_at', date('Y-m-d H:i:s'))
                    ->update();
                continue;
            }

            $rawMaterial = $rawMaterialModel->find((int) $selectedValue);
            if (!$rawMaterial) {
                return redirect()->to('/pos/history')->with('error', 'Salah satu bahan baku tidak ditemukan.');
            }

            if ((float) $rawMaterial['stock'] < $used) {
                return redirect()->to('/pos/history')->with('error', 'Stok bahan baku ' . $rawMaterial['name'] . ' tidak cukup untuk dipakai.');
            }

            $db->table('sale_material_report_details')->insert([
                'report_id' => $reportId,
                'raw_material_id' => (int) $selectedValue,
                'raw_material_name' => $rawMaterial['name'],
                'raw_material_unit' => $rawMaterial['unit'],
                'qty_used' => $used,
            ]);

            $db->table('raw_materials')->where('id', (int) $selectedValue)
                ->set('stock', 'stock - ' . $used, false)
                ->set('stock_updated_at', date('Y-m-d H:i:s'))
                ->update();
        }

        return redirect()->to('/pos/history')->with('success', 'Laporan bahan baku berhasil disimpan dan status transaksi diubah menjadi selesai.');
    }
}