<?php
namespace App\Models;
use CodeIgniter\Model;

class RawMaterialModel extends Model
{
    protected $table            = 'raw_materials';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['sku', 'name', 'unit', 'stock', 'stock_updated_at'];
}