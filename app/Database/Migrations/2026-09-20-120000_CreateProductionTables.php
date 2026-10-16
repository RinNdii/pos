<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductionTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'item_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'item_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'qty' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'notes' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('item_id', 'items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('production_batches', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'production_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'raw_material_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'raw_material_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'raw_material_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'qty_used' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('production_id', 'production_batches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('raw_material_id', 'raw_materials', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('production_details', true);
    }

    public function down()
    {
        $this->forge->dropTable('production_details', true);
        $this->forge->dropTable('production_batches', true);
    }
}
