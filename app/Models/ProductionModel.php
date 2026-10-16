<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductionModel extends Model
{
    protected $table            = 'production_batches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['item_id', 'item_name', 'qty', 'notes', 'created_at'];
    protected $useTimestamps    = false;
}
