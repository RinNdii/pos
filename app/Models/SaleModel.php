<?php namespace App\Models;
use CodeIgniter\Model;
class SaleModel extends Model {
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $allowedFields = ['invoice_number', 'customer_id', 'sale_date', 'payment_method', 'transfer_bank', 'discount', 'total_amount'];
}