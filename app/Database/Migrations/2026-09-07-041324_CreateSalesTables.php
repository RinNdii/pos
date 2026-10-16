<?php namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateSalesSystem extends Migration {
    public function up() {
        // Tabel Utama Penjualan
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'invoice_number' => ['type' => 'VARCHAR', 'constraint' => 50],
            'customer_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'sale_date'      => ['type' => 'DATETIME'],
            'discount'      => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'total_amount'   => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('sales', true);

        // Tabel Detail Penjualan
        $this->forge->addField([
            'id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'sale_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'item_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'qty'      => ['type' => 'INT', 'constraint' => 11],
            'price'    => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('sale_details', true);
    }
    public function down() {
        $this->forge->dropTable('sale_details');
        $this->forge->dropTable('sales');
    }
}