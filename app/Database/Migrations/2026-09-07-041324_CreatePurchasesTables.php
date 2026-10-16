<?php namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreatePurchasesTables extends Migration {
    public function up() {
        // Tabel Header Purchases
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'po_number' => ['type' => 'VARCHAR', 'constraint' => 50],
            'supplier_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'po_date' => ['type' => 'DATE'],
            'status' => ['type' => 'ENUM', 'constraint' => ['Pending', 'Completed'], 'default' => 'Pending'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('supplier_id', 'suppliers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchases');

        // Tabel Detail Purchases
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'purchase_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'item_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'qty' => ['type' => 'INT', 'constraint' => 11],
            'price' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('purchase_id', 'purchases', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('purchase_details');
    }
    public function down() {
        $this->forge->dropTable('purchase_details');
        $this->forge->dropTable('purchases');
    }
}