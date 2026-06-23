<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'default' => null],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'       => ['type' => 'ENUM', 'constraint' => ['warga', 'admin_instansi', 'super_admin'], 'default' => 'warga'],
            'created_at' => ['type' => 'DATETIME', 'null' => false, 'default' => null],
            'updated_at' => ['type' => 'DATETIME', 'null' => false, 'default' => null],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');
    }

    public function down(): void
    {
        $this->forge->dropTable('users');
    }
}
