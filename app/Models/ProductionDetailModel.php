<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductionDetailModel extends Model
{
    protected $table            = 'production_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['production_id', 'raw_material_id', 'raw_material_name', 'raw_material_unit', 'qty_used'];
    protected $useTimestamps    = false;
}
