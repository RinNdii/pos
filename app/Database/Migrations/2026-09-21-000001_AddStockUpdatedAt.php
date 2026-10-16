<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStockUpdatedAt extends Migration
{
    public function up()
    {
        $this->forge->addColumn('items', [
            'stock_updated_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'stock'],
        ]);
        $this->forge->addColumn('raw_materials', [
            'stock_updated_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'stock'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('items', 'stock_updated_at');
        $this->forge->dropColumn('raw_materials', 'stock_updated_at');
    }
}