<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentMethodToSales extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sales', [
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'Tunai',
                'after'      => 'sale_date',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sales', 'payment_method');
    }
}