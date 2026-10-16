<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSaleMaterialReports extends Migration
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
            'sale_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
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
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('sale_id', 'sales', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sale_material_reports', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'report_id' => [
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
        $this->forge->addForeignKey('report_id', 'sale_material_reports', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('raw_material_id', 'raw_materials', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sale_material_report_details', true);
    }

    public function down()
    {
        $this->forge->dropTable('sale_material_report_details', true);
        $this->forge->dropTable('sale_material_reports', true);
    }
}
