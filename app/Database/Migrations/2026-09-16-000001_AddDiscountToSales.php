<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiscountToSales extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sales', [
            'discount' => [
                'type'    => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'after'   => 'sale_date',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sales', 'discount');
    }
}