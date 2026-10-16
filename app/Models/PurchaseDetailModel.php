<?php
namespace App\Models;
use CodeIgniter\Model;

class PurchaseDetailModel extends Model {
    protected $table         = 'purchase_details';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['purchase_id', 'item_id', 'raw_material_id', 'product_name', 'unit', 'purchase_price', 'qty'];
    protected $returnType    = 'array';
}