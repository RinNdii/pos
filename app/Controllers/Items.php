<?php
namespace App\Controllers;
use App\Models\ItemModel;

class Items extends BaseController
{
    public function index()
    {
        $model = new ItemModel();
        $data['items'] = $model->findAll();

        $lastItem = $model->orderBy('id', 'DESC')->first();
        $nextId = $lastItem ? $lastItem['id'] + 1 : 1;
        $data['auto_sku'] = 'BRG' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('items/index', $data);
    }

    public function create()
    {
        $model = new ItemModel();
        $lastItem = $model->orderBy('id', 'DESC')->first();
        $nextId = $lastItem ? $lastItem['id'] + 1 : 1;

        return view('items/create', [
            'auto_sku' => 'BRG' . str_pad($nextId, 4, '0', STR_PAD_LEFT)
        ]);
    }

    public function store()
    {
        $model = new ItemModel();
        $data = [
            'sku'            => $this->request->getPost('sku'),
            'name'           => $this->request->getPost('name'),
            'unit'           => $this->request->getPost('unit'),
            'purchase_price' => 0,
            'selling_price'  => $this->request->getPost('selling_price'),
            'stock'          => $this->request->getPost('stock'),
            'image'          => $this->saveImage()
        ];
        $model->insert($data);
        return redirect()->to('/items')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update($id)
    {
        $model = new ItemModel();
        $data = [
            'name'          => $this->request->getPost('name'),
            'unit'          => $this->request->getPost('unit'),
            'selling_price' => $this->request->getPost('selling_price'),
            'stock'         => $this->request->getPost('stock'),
            'stock_updated_at' => date('Y-m-d H:i:s')
        ];
        $image = $this->saveImage();
        if ($image) {
            $data['image'] = $image;
        }
        $model->update($id, $data);
        return redirect()->to('/items')->with('success', 'Data barang berhasil diubah.');
    }

    private function saveImage(): ?string
    {
        $image = $this->request->getFile('image');
        if (!$image || !$image->isValid() || $image->hasMoved()) {
            return null;
        }

        if (!in_array($image->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true) || $image->getSizeByUnit('mb') > 2) {
            return null;
        }

        $directory = FCPATH . 'uploads/products';
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = $image->getRandomName();
        $image->move($directory, $filename);
        return 'uploads/products/' . $filename;
    }

    public function delete($id)
    {
        $model = new ItemModel();
        $model->delete($id);
        return redirect()->to('/items')->with('success', 'Barang berhasil dihapus.');
    }
}