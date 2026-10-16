<?php
namespace App\Controllers;
use App\Models\SupplierModel;

class Suppliers extends BaseController
{
    public function index()
    {
        $model = new SupplierModel();
        $data['suppliers'] = $model->findAll();

        return view('suppliers/index', $data);
    }

    public function store()
    {
        $model = new SupplierModel();
        $data = [
            'name'    => $this->request->getPost('name'),
            'phone'   => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];
        $model->insert($data);
        return redirect()->to('/suppliers')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update($id)
    {
        $model = new SupplierModel();
        $data = [
            'name'    => $this->request->getPost('name'),
            'phone'   => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];
        $model->update($id, $data);
        return redirect()->to('/suppliers')->with('success', 'Data supplier berhasil diubah.');
    }

    public function delete($id)
    {
        $model = new SupplierModel();
        $model->delete($id);
        return redirect()->to('/suppliers')->with('success', 'Supplier berhasil dihapus.');
    }
}