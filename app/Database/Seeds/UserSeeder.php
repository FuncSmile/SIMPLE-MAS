<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $password = password_hash('admin123', PASSWORD_DEFAULT);

        $users = [
            [
                'name'       => 'Super Admin',
                'email'      => 'superadmin@simpelmas.id',
                'phone'      => '081234567890',
                'password'   => $password,
                'role'       => 'super_admin',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Admin Dinas PU',
                'email'      => 'admin_pu@simpelmas.id',
                'phone'      => '081234567891',
                'password'   => $password,
                'role'       => 'admin_instansi',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Admin Lingkungan',
                'email'      => 'admin_lingkungan@simpelmas.id',
                'phone'      => '081234567892',
                'password'   => $password,
                'role'       => 'admin_instansi',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $builder = $this->db->table('users');
        foreach ($users as $user) {
            $builder->insert($user);
        }
    }
}
