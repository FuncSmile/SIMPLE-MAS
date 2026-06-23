<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddComplaintsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'description' => ['type' => 'TEXT'],
            'photo_before' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'photo_after'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'lat'          => ['type' => 'DOUBLE'],
            'lng'          => ['type' => 'DOUBLE'],
            'status'       => ['type' => 'ENUM', 'constraint' => ['pending', 'in_progress', 'resolved', 'rejected'], 'default' => 'pending'],
            'upvotes'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => false, 'default' => null],
            'updated_at'   => ['type' => 'DATETIME', 'null' => false, 'default' => null],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('complaints');

        $db = \Config\Database::connect();
        $db->query('ALTER TABLE complaints ADD COLUMN location POINT NOT NULL SRID 4326 AFTER lng');
        $db->query('ALTER TABLE complaints ADD SPATIAL INDEX idx_location (location)');
    }

    public function down(): void
    {
        $this->forge->dropTable('complaints');
    }
}
