<?php

namespace App\Controllers;

use App\Models\ItemModel;
use App\Models\ProductionDetailModel;
use App\Models\ProductionModel;
use App\Models\RawMaterialModel;

class Production extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        if (!$db->tableExists('production_batches') || !$db->tableExists('production_details')) {
            return view('production/index', ['productions' => []])
                ->with('error', 'Tabel produksi belum dibuat. Jalankan migrasi database terlebih dahulu: php spark migrate');
        }

        $productions = $db->table('production_batches')
            ->select('production_batches.*, items.name as item_name, items.unit as item_unit')
            ->join('items', 'items.id = production_batches.item_id', 'left')
            ->orderBy('production_batches.created_at', 'DESC')
            ->get()->getResultArray();

        foreach ($productions as &$production) {
            $production['materials'] = $db->table('production_details')
                ->select('production_details.*, raw_materials.name as raw_material_name, raw_materials.unit as raw_material_unit')
                ->join('raw_materials', 'raw_materials.id = production_details.raw_material_id', 'left')
                ->where('production_id', $production['id'])
                ->get()->getResultArray();
        }
        unset($production);

        return view('production/index', ['productions' => $productions]);
    }

    public function create()
    {
        $itemModel = new ItemModel();
        $rawMaterialModel = new RawMaterialModel();
        $db = \Config\Database::connect();

        $rawMaterials = $rawMaterialModel->orderBy('name', 'ASC')->findAll();
        $manualWarehouseItems = $db->table('purchase_details')
            ->select('purchase_details.product_name as name, purchase_details.unit, COALESCE(SUM(purchase_details.qty), 0) as stock')
            ->join('purchases', 'purchases.id = purchase_details.purchase_id')
            ->join('raw_materials custom_raw', 'custom_raw.name = purchase_details.product_name AND custom_raw.unit = purchase_details.unit', 'left')
            ->where('purchase_details.item_id IS NULL', null, false)
            ->where('purchase_details.raw_material_id IS NULL', null, false)
            ->where('purchase_details.product_name IS NOT NULL', null, false)
            ->where('purchases.status', 'Diterima')
            ->where('custom_raw.id IS NULL', null, false)
            ->groupBy('purchase_details.product_name, purchase_details.unit')
            ->orderBy('purchase_details.product_name', 'ASC')
            ->get()->getResultArray();

        $warehouseMaterials = [];

        foreach ($rawMaterials as $material) {
            $warehouseMaterials[] = [
                'id' => (int) $material['id'],
                'name' => $material['name'],
                'unit' => $material['unit'],
                'stock' => (int) ($material['stock'] ?? 0),
                'type' => 'raw_material',
            ];
        }

        foreach ($manualWarehouseItems as $material) {
            $warehouseMaterials[] = [
                'id' => null,
                'name' => $material['name'],
                'unit' => $material['unit'],
                'stock' => (int) ($material['stock'] ?? 0),
                'type' => 'custom',
            ];
        }

        usort($warehouseMaterials, function ($a, $b) {
            return strcmp(($a['name'] ?? ''), ($b['name'] ?? ''));
        });

        return view('production/create', [
            'items' => $itemModel->findAll(),
            'raw_materials' => $warehouseMaterials,
        ]);
    }

    public function store()
    {
        $db = \Config\Database::connect();

        if (!$db->tableExists('production_batches') || !$db->tableExists('production_details')) {
            return redirect()->to('/production/create')->with('error', 'Tabel produksi belum dibuat. Jalankan migrasi database: php spark migrate');
        }

        $itemId = (int) $this->request->getPost('item_id');
        $qty = (int) $this->request->getPost('qty');
        $notes = trim((string) $this->request->getPost('notes'));
        $rawMaterialIds = $this->request->getPost('raw_id') ?? [];
        $manualNames = $this->request->getPost('raw_material_name_manual') ?? [];
        $manualUnits = $this->request->getPost('raw_material_unit_manual') ?? [];
        $usedQtys = $this->request->getPost('raw_qty') ?? [];

        if ($itemId <= 0 || $qty <= 0) {
            return redirect()->to('/production/create')->with('error', 'Produk dan jumlah hasil produksi harus diisi dengan benar.');
        }

        if (empty($rawMaterialIds) && empty($manualNames)) {
            return redirect()->to('/production/create')->with('error', 'Pilih minimal satu bahan baku yang dipakai atau masukkan bahan baku manual.');
        }

        $itemModel = new ItemModel();
        $rawMaterialModel = new RawMaterialModel();
        $productionModel = new ProductionModel();
        $productionDetailModel = new ProductionDetailModel();
        $db = \Config\Database::connect();

        $item = $itemModel->find($itemId);
        if (!$item) {
            return redirect()->to('/production/create')->with('error', 'Produk yang dipilih tidak ditemukan.');
        }

        $consumption = [];
        foreach ($rawMaterialIds as $index => $rawMaterialId) {
            $used = (float) ($usedQtys[$index] ?? 0);
            if ($used <= 0) {
                continue;
            }

            $selectedValue = trim((string) $rawMaterialId);

            if (empty($selectedValue) || $selectedValue === 'manual') {
                $manualName = trim((string) ($manualNames[$index] ?? ''));
                $manualUnit = trim((string) ($manualUnits[$index] ?? '')) ?: 'pcs';

                if ($manualName === '') {
                    return redirect()->to('/production/create')->with('error', 'Nama bahan baku manual harus diisi.');
                }

                $existingManualRawMaterial = $rawMaterialModel
                    ->where('LOWER(name)', strtolower($manualName))
                    ->where('LOWER(unit)', strtolower($manualUnit))
                    ->first();

                if ($existingManualRawMaterial) {
                    $rawMaterialIdForStock = (int) $existingManualRawMaterial['id'];
                    $rawMaterialName = $existingManualRawMaterial['name'];
                    $rawMaterialUnit = $existingManualRawMaterial['unit'] ?: $manualUnit;
                    $stockAvailable = (float) ($existingManualRawMaterial['stock'] ?? 0);
                } else {
                    $rawMaterialIdForStock = (int) $rawMaterialModel->insert([
                        'sku' => 'manual-' . time() . '-' . ($index + 1),
                        'name' => $manualName,
                        'unit' => $manualUnit,
                        'stock' => 0,
                    ], true);
                    $rawMaterialName = $manualName;
                    $rawMaterialUnit = $manualUnit;
                    $stockAvailable = 0;
                }

                if ($stockAvailable < $used) {
                    return redirect()->to('/production/create')->with('error', 'Stok bahan baku ' . $rawMaterialName . ' tidak cukup untuk dipakai.');
                }

                $consumption[] = [
                    'raw_material_id' => $rawMaterialIdForStock,
                    'raw_material_name' => $rawMaterialName,
                    'raw_material_unit' => $rawMaterialUnit,
                    'qty_used' => $used,
                ];
                continue;
            }

            if (str_starts_with($selectedValue, 'custom:')) {
                $customValue = urldecode(substr($selectedValue, 7));
                [$customName, $customUnit] = array_pad(explode('|', $customValue, 2), 2, '');
                $customName = trim((string) $customName);
                $customUnit = trim((string) ($customUnit ?: 'pcs'));

                if ($customName === '') {
                    return redirect()->to('/production/create')->with('error', 'Nama bahan custom dari gudang harus diisi.');
                }

                $existingCustomRawMaterial = $rawMaterialModel
                    ->where('LOWER(name)', strtolower($customName))
                    ->where('LOWER(unit)', strtolower($customUnit))
                    ->first();

                if ($existingCustomRawMaterial) {
                    $rawMaterialIdForStock = (int) $existingCustomRawMaterial['id'];
                    $rawMaterialName = $existingCustomRawMaterial['name'];
                    $rawMaterialUnit = $existingCustomRawMaterial['unit'] ?: $customUnit;
                    $stockAvailable = (float) ($existingCustomRawMaterial['stock'] ?? 0);
                } else {
                    $legacyCustomStock = $db->table('purchase_details')
                        ->selectSum('purchase_details.qty', 'stock')
                        ->join('purchases', 'purchases.id = purchase_details.purchase_id')
                        ->where('purchase_details.item_id IS NULL', null, false)
                        ->where('purchase_details.raw_material_id IS NULL', null, false)
                        ->where('purchase_details.product_name', $customName)
                        ->where('purchase_details.unit', $customUnit)
                        ->where('purchases.status', 'Diterima')
                        ->get()->getRowArray();
                    $stockAvailable = (float) ($legacyCustomStock['stock'] ?? 0);
                    $rawMaterialIdForStock = (int) $rawMaterialModel->insert([
                        'sku' => 'cust-' . time() . '-' . ($index + 1),
                        'name' => $customName,
                        'unit' => $customUnit,
                        'stock' => $stockAvailable,
                    ], true);
                    $rawMaterialName = $customName;
                    $rawMaterialUnit = $customUnit;
                }

                if ($stockAvailable < $used) {
                    return redirect()->to('/production/create')->with('error', 'Stok bahan baku ' . $rawMaterialName . ' tidak cukup untuk dipakai.');
                }

                $consumption[] = [
                    'raw_material_id' => $rawMaterialIdForStock,
                    'raw_material_name' => $rawMaterialName,
                    'raw_material_unit' => $rawMaterialUnit,
                    'qty_used' => $used,
                ];
                continue;
            }

            $rawMaterial = $rawMaterialModel->find((int) $selectedValue);
            if (!$rawMaterial) {
                return redirect()->to('/production/create')->with('error', 'Salah satu bahan baku tidak ditemukan.');
            }

            if ((float) $rawMaterial['stock'] < $used) {
                return redirect()->to('/production/create')->with('error', 'Stok bahan baku ' . $rawMaterial['name'] . ' tidak cukup untuk dipakai.');
            }

            $consumption[] = [
                'raw_material_id' => (int) $selectedValue,
                'raw_material_name' => $rawMaterial['name'],
                'raw_material_unit' => $rawMaterial['unit'],
                'qty_used' => $used,
            ];
        }

        if (empty($consumption)) {
            return redirect()->to('/production/create')->with('error', 'Masukkan jumlah bahan baku yang valid untuk proses produksi.');
        }

        try {
            $db->transStart();

            $productionId = $productionModel->insert([
                'item_id' => $itemId,
                'item_name' => $item['name'],
                'qty' => $qty,
                'notes' => $notes,
                'created_at' => date('Y-m-d H:i:s'),
            ], true);

            foreach ($consumption as $material) {
                $productionDetailModel->insert([
                    'production_id' => $productionId,
                    'raw_material_id' => $material['raw_material_id'],
                    'raw_material_name' => $material['raw_material_name'],
                    'raw_material_unit' => $material['raw_material_unit'],
                    'qty_used' => $material['qty_used'],
                ]);

                if (!empty($material['raw_material_id'])) {
                    $currentRawMaterial = $rawMaterialModel->find($material['raw_material_id']);
                    $rawMaterialModel->update($material['raw_material_id'], [
                        'stock' => ((float) $currentRawMaterial['stock']) - $material['qty_used'],
                        'stock_updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $currentItemStock = (float) ($item['stock'] ?? 0);
            $itemModel->update($itemId, [
                'stock' => $currentItemStock + $qty,
                'stock_updated_at' => date('Y-m-d H:i:s'),
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->to('/production/create')->with('error', 'Gagal menyimpan data produksi.');
            }

            return redirect()->to('/production')->with('success', 'Produksi berhasil disimpan dan stok bahan baku telah dikurangi.');
        } catch (\Throwable $e) {
            if ($db->transDepth() > 0) {
                $db->transRollback();
            }

            return redirect()->to('/production/create')->with('error', 'Gagal menyimpan data produksi: ' . $e->getMessage());
        }
    }
}
