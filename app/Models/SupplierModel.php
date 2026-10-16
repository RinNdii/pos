<?php
namespace App\Models;
use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table         = 'suppliers';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'phone', 'address'];
    protected $returnType    = 'array';
}