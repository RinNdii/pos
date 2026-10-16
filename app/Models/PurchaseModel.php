<?php
namespace App\Models;
use CodeIgniter\Model;

class PurchaseModel extends Model {
    protected $table         = 'purchases';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['po_number', 'supplier_id', 'po_date', 'total_items', 'status'];
    protected $returnType    = 'array';
}