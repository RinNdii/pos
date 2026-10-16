<?php
namespace App\Controllers;
use App\Models\CustomerModel;

class Customers extends BaseController
{
    public function index()
    {
        $model = new CustomerModel();
        $data['customers'] = $model->findAll();

        return view('customers/index', $data);
    }

    public function store()
    {
        $model = new CustomerModel();
        $data = [
            'name'    => $this->request->getPost('name'),
            'phone'   => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];
        $model->insert($data);
        return redirect()->to('/customers')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function update($id)
    {
        $model = new CustomerModel();
        $data = [
            'name'    => $this->request->getPost('name'),
            'phone'   => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];
        $model->update($id, $data);
        return redirect()->to('/customers')->with('success', 'Data customer berhasil diubah.');
    }

    public function delete($id)
    {
        $model = new CustomerModel();
        $model->delete($id);
        return redirect()->to('/customers')->with('success', 'Customer berhasil dihapus.');
    }
}