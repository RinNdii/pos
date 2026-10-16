<?php namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateReceivingsTables extends Migration {
    public function up() {
        // Tabel Header Receivings
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'receipt_number' => ['type' => 'VARCHAR', 'constraint' => 50],
            'purchase_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'receive_date' => ['type' => 'DATE'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('purchase_id', 'purchases', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('receivings');

        // Tabel Detail Receivings
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'receiving_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'item_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'qty_received' => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('receiving_id', 'receivings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('receiving_details');
    }
    public function down() {
        $this->forge->dropTable('receiving_details');
        $this->forge->dropTable('receivings');
    }
}