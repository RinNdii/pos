<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTransferBankToSales extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sales', [
            'transfer_bank' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'payment_method',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sales', 'transfer_bank');
    }
}