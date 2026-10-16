<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubtotalToSaleDetails extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sale_details', [
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'after'      => 'price',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sale_details', 'subtotal');
    }
}
