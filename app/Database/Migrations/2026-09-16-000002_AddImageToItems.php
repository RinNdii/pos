<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageToItems extends Migration
{
    public function up()
    {
        $this->forge->addColumn('items', [
            'image' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'stock']
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('items', 'image');
    }
}