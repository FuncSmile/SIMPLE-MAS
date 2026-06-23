<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoriesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true, 'default' => null],
            'agency'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'created_at'  => ['type' => 'DATETIME', 'null' => false, 'default' => null],
            'updated_at'  => ['type' => 'DATETIME', 'null' => false, 'default' => null],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('categories');
    }

    public function down(): void
    {
        $this->forge->dropTable('categories');
    }
}
