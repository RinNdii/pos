<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table            = 'items';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['sku', 'name', 'unit', 'purchase_price', 'selling_price', 'stock', 'stock_updated_at', 'image'];
    protected $returnType       = 'array';
    }
