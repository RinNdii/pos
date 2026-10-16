<?php namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateRawMaterialsTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'sku'   => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'name'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'unit'  => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Kg'],
            'stock' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('raw_materials', true);
    }
    public function down() {
        $this->forge->dropTable('raw_materials');
    }
}