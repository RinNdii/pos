<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPurchasePriceToPurchaseDetails extends Migration
{
    public function up()
    {
        $this->forge->addColumn('purchase_details', [
            'purchase_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
                'after'      => 'unit',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('purchase_details', 'purchase_price');
    }
}