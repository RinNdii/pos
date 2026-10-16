<?php
namespace App\Controllers;
use App\Models\PurchaseModel;
use App\Models\PurchaseDetailModel;
use App\Models\SupplierModel;
use App\Models\ItemModel;
use App\Models\RawMaterialModel;

class Purchases extends BaseController {
    public function index() {
        $db = \Config\Database::connect();
        // Join dengan tabel supplier untuk menampilkan nama supplier
        $data['purchases'] = $db->table('purchases')
                    ->select('purchases.*, suppliers.name as supplier_name, GROUP_CONCAT(DISTINCT COALESCE(items.name, raw_materials.name, purchase_details.product_name) SEPARATOR ", ") as product_names')
                                ->join('suppliers', 'suppliers.id = purchases.supplier_id', 'left')
                    ->join('purchase_details', 'purchase_details.purchase_id = purchases.id', 'left')
                    ->join('items', 'items.id = purchase_details.item_id', 'left')
                    ->join('raw_materials', 'raw_materials.id = purchase_details.raw_material_id', 'left')
                    ->groupBy('purchases.id, purchases.po_number, purchases.supplier_id, purchases.po_date, purchases.total_items, purchases.status, suppliers.name')
                                ->orderBy('po_date', 'DESC')
                                ->get()->getResultArray();
        return view('purchases/index', $data);
    }

    public function create() {
        $supplierModel = new SupplierModel();
        $rawMaterialModel = new RawMaterialModel();
        $itemModel = new ItemModel();
        
        $data['suppliers'] = $supplierModel->findAll();
        $data['items'] = $itemModel->findAll();
        $data['raw_materials'] = $rawMaterialModel->findAll();
        $data['po_number'] = 'PO-' . date('Ymd') . '-' . rand(100, 999);
        
        return view('purchases/create', $data);
    }

    public function store() {
        $purchaseModel = new PurchaseModel();
        $detailModel   = new PurchaseDetailModel();
        $itemModel     = new ItemModel();

        // 1. Simpan Header PO
        $purchaseData = [
            'po_number'   => $this->request->getPost('po_number'),
            'supplier_id' => $this->request->getPost('supplier_id'),
            'po_date'     => $this->request->getPost('po_date'),
            'status'      => 'Pending'
        ];
        $purchaseModel->insert($purchaseData);
        $purchaseId = $purchaseModel->insertID();

        // 2. Simpan Detail Item
        $productSources = $this->request->getPost('product_source') ?? [];
        $productNames = $this->request->getPost('product_name') ?? [];
        $units = $this->request->getPost('unit') ?? [];
        $purchasePrices = $this->request->getPost('purchase_price') ?? [];
        $qtys  = $this->request->getPost('qty') ?? [];
        $totalItems = 0;

        foreach ($qtys as $key => $qty) {
            $productSource = $productSources[$key] ?? '';
            $itemId = null;
            $rawMaterialId = null;
            $productName = trim($productNames[$key] ?? '');
            $unit = trim($units[$key] ?? '');
            $purchasePrice = (float) ($purchasePrices[$key] ?? 0);

            if (str_starts_with($productSource, 'item:')) {
                $itemId = substr($productSource, 5);
            } elseif (str_starts_with($productSource, 'raw_material:')) {
                $rawMaterialId = substr($productSource, 13);
            } elseif ($productSource !== 'manual') {
                continue;
            }

            if ($productName === '' || $unit === '' || $purchasePrice < 0 || (int) $qty <= 0) {
                continue;
            }

            $detailModel->insert([
                'purchase_id' => $purchaseId,
                'item_id' => $itemId ?: null,
                'raw_material_id' => $rawMaterialId ?: null,
                'product_name' => $productName,
                'unit' => $unit,
                'purchase_price' => $purchasePrice,
                'qty' => (int) $qty
            ]);
            $totalItems += (int) $qty;
        }

        // 3. Update total item di header
        $purchaseModel->update($purchaseId, ['total_items' => $totalItems]);

        return redirect()->to('/purchases')->with('success', 'Purchase Order berhasil dibuat.');
    }
}