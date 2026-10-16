<?php
namespace App\Controllers;
use App\Models\RawMaterialModel;

class RawMaterials extends BaseController
{
    protected $rawMaterialModel;

    public function __construct()
    {
        $this->rawMaterialModel = new RawMaterialModel();
    }

    public function index()
    {
        // Logika Generate Auto SKU (Format: BB-001)
        $lastItem = $this->rawMaterialModel->orderBy('id', 'DESC')->first();
        if ($lastItem && !empty($lastItem['sku'])) {
            // Mengambil angka dari SKU terakhir (membuang 3 karakter awal "BB-")
            $lastNumber = (int) substr($lastItem['sku'], 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        $auto_sku = 'BB-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $data = [
            'title'         => 'Master Bahan Baku',
            'raw_materials' => $this->rawMaterialModel->findAll(),
            'auto_sku'      => $auto_sku // Kirim auto SKU ke View
        ];
        
        return view('raw_materials/index', $data);
    }

    public function create()
    {
        $lastItem = $this->rawMaterialModel->orderBy('id', 'DESC')->first();
        $nextNumber = $lastItem && !empty($lastItem['sku'])
            ? (int) substr($lastItem['sku'], 3) + 1
            : 1;

        return view('raw_materials/create', [
            'auto_sku' => 'BB-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT)
        ]);
    }

    public function store()
    {
        $this->rawMaterialModel->insert([
            'sku'           => $this->request->getPost('sku'),
            'name'          => $this->request->getPost('name'),
            'unit'          => $this->request->getPost('unit'),
        ]);
        return redirect()->to('/raw_materials')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function update($id)
    {
        $this->rawMaterialModel->update($id, [
            'name'          => $this->request->getPost('name'),
            'unit'          => $this->request->getPost('unit'),
        ]);
        return redirect()->to('/raw_materials')->with('success', 'Bahan baku berhasil diubah.');
    }

    public function delete($id)
    {
        $this->rawMaterialModel->delete($id);
        return redirect()->to('/raw_materials')->with('success', 'Bahan baku berhasil dihapus.');
    }
}