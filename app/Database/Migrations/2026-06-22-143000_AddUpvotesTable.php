<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpvotesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'complaint_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => false, 'default' => null],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('complaint_id', 'complaints', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['user_id', 'complaint_id']);
        $this->forge->createTable('upvotes');
    }

    public function down(): void
    {
        $this->forge->dropTable('upvotes');
    }
}
