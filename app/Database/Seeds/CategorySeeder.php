<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Infrastruktur',
                'description' => 'Kerusakan jalan, jembatan, trotoar, dan fasilitas umum lainnya',
                'agency'      => 'Dinas Pekerjaan Umum',
            ],
            [
                'name'        => 'Sampah',
                'description' => 'Penumpukan sampah, TPS liar, dan masalah kebersihan lingkungan',
                'agency'      => 'Dinas Lingkungan Hidup',
            ],
            [
                'name'        => 'Penerangan Jalan',
                'description' => 'Lampu jalan mati, kerusakan PJU, dan area gelap',
                'agency'      => 'Dinas Perhubungan',
            ],
            [
                'name'        => 'Banjir',
                'description' => 'Genangan air, saluran tersumbat, dan luapan sungai',
                'agency'      => 'Dinas Pengairan',
            ],
            [
                'name'        => 'Keamanan',
                'description' => 'Gangguan ketertiban, vandalisme, dan potensi kriminalitas',
                'agency'      => 'Satpol PP',
            ],
            [
                'name'        => 'Lainnya',
                'description' => 'Pengaduan lain di luar kategori yang tersedia',
                'agency'      => 'Administrasi Umum',
            ],
        ];

        $builder = $this->db->table('categories');
        foreach ($categories as $category) {
            $category['created_at'] = date('Y-m-d H:i:s');
            $category['updated_at'] = date('Y-m-d H:i:s');
            $builder->insert($category);
        }
    }
}
