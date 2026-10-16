<?php
namespace App\Controllers;
use App\Models\ItemModel;
use App\Models\CustomerModel;
use App\Models\PurchaseModel;

class Home extends BaseController
{
    public function index()
    {
        $itemModel     = new ItemModel();
        $customerModel = new CustomerModel();
        $purchaseModel = new PurchaseModel();

        
        $data['total_items']     = $itemModel->countAllResults();
        $data['total_customers'] = $customerModel->countAllResults();
        $data['total_purchases'] = $purchaseModel->countAllResults(); 

        return view('dashboard', $data); 
    }
}