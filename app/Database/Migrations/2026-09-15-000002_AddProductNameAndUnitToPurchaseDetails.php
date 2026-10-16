<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductNameAndUnitToPurchaseDetails extends Migration
{
    public function up()
    {
        $this->forge->addColumn('purchase_details', [
            'product_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'raw_material_id',
            ],
            'unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'product_name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('purchase_details', ['product_name', 'unit']);
    }
}