<?php namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class AddUnitToItems extends Migration {
    public function up() {
        $this->forge->addColumn('items', [
            'unit' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Pcs', 'after' => 'name']
        ]);
    }
    public function down() {
        $this->forge->dropColumn('items', 'unit');
    }
}