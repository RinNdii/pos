<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRawMaterialToPurchaseDetails extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('purchase_details', [
            'item_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        $this->forge->addColumn('purchase_details', [
            'raw_material_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'item_id',
            ],
        ]);

        $this->forge->addForeignKey('raw_material_id', 'raw_materials', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('purchase_details', 'purchase_details_raw_material_id_foreign');
        $this->forge->dropColumn('purchase_details', 'raw_material_id');
        $this->forge->modifyColumn('purchase_details', [
            'item_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
    }
}
