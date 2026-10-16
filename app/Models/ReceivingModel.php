<?php
namespace App\Models;
use CodeIgniter\Model;

class ReceivingModel extends Model {
    protected $table         = 'receivings';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['receive_number', 'purchase_id', 'receive_date'];
    protected $returnType    = 'array';
}