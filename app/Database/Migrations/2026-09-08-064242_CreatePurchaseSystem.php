<?php namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreatePurchaseSystem extends Migration {
    public function up() {
        // Tabel Utama Pembelian
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'po_number'   => ['type' => 'VARCHAR', 'constraint' => 50],
            'supplier_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'po_date'     => ['type' => 'DATE'],
            'total_items' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status'      => ['type' => 'ENUM', 'constraint' => ['Pending', 'Diterima'], 'default' => 'Pending'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('purchases');

        // Tabel Detail Pembelian
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'purchase_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'item_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'qty'         => ['type' => 'INT', 'constraint' => 11],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('purchase_details');
    }
    public function down() {
        $this->forge->dropTable('purchase_details, true');
        $this->forge->dropTable('purchases, true');
    }
}