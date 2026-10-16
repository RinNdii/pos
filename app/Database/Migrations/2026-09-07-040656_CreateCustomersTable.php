<?php namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateCustomersTable extends Migration {
    public function up() {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'        => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'      => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('customers');
    }
    public function down() {
        $this->forge->dropTable('customers');
    }
}