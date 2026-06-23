<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    private array $indonesianCities = [
        ['city' => 'Jakarta', 'lat' => -6.2088, 'lng' => 106.8456],
        ['city' => 'Surabaya', 'lat' => -7.2575, 'lng' => 112.7521],
        ['city' => 'Bandung', 'lat' => -6.9175, 'lng' => 107.6191],
        ['city' => 'Medan', 'lat' => 3.5952, 'lng' => 98.6722],
        ['city' => 'Semarang', 'lat' => -6.9667, 'lng' => 110.4167],
        ['city' => 'Makassar', 'lat' => -5.1477, 'lng' => 119.4322],
        ['city' => 'Yogyakarta', 'lat' => -7.7971, 'lng' => 110.3688],
        ['city' => 'Denpasar', 'lat' => -8.6500, 'lng' => 115.2167],
        ['city' => 'Palembang', 'lat' => -2.9761, 'lng' => 104.7754],
        ['city' => 'Pekanbaru', 'lat' => 0.5071, 'lng' => 101.4478],
        ['city' => 'Banjarmasin', 'lat' => -3.3167, 'lng' => 114.5833],
        ['city' => 'Manado', 'lat' => 1.4917, 'lng' => 124.8428],
        ['city' => 'Padang', 'lat' => -0.9471, 'lng' => 100.4172],
        ['city' => 'Bogor', 'lat' => -6.5946, 'lng' => 106.7892],
        ['city' => 'Malang', 'lat' => -7.9797, 'lng' => 112.6304],
        ['city' => 'Solo', 'lat' => -7.5667, 'lng' => 110.8167],
        ['city' => 'Pontianak', 'lat' => -0.0222, 'lng' => 109.3425],
        ['city' => 'Banda Aceh', 'lat' => 5.5483, 'lng' => 95.3238],
        ['city' => 'Jayapura', 'lat' => -2.5333, 'lng' => 140.7000],
        ['city' => 'Balikpapan', 'lat' => -1.2667, 'lng' => 116.8333],
        ['city' => 'Mataram', 'lat' => -8.5833, 'lng' => 116.1167],
        ['city' => 'Kupang', 'lat' => -10.1667, 'lng' => 123.6000],
        ['city' => 'Ambon', 'lat' => -3.6954, 'lng' => 128.1814],
        ['city' => 'Tangerang', 'lat' => -6.1781, 'lng' => 106.6300],
        ['city' => 'Batam', 'lat' => 1.1300, 'lng' => 104.0500],
    ];

    private array $complaintDescriptions = [
        'Jalan berlubang besar membahayakan pengendara',
        'Tumpukan sampah sudah berhari-hari tidak diangkut',
        'Lampu penerangan jalan mati total selama seminggu',
        'Saluran air tersumbat menyebabkan genangan saat hujan',
        'Trotoar rusak dan tidak layak untuk pejalan kaki',
        'Tiang listrik miring dan berpotensi roboh',
        'Gorong-gorong terbuka tanpa penutup',
        'Pohon tumbang menutupi akses jalan',
        'Bau tidak sedap dari tempat pembuangan sampah liar',
        'Marka jalan sudah pudar tidak terlihat',
        'Rambu lalu lintas rusak dan tidak terbaca',
        'Genangan air di depan pemukiman warga',
        'Kabel listrik menjuntai rendah membahayakan',
        'Jembatan gantung mengalami kerusakan parah',
        'Fasilitas taman umum rusak dan tidak terawat',
        'Saluran drainase mampet dan meluap',
        'Lampu taman mati sehingga area rawan kejahatan',
        'Jalan lingkungan mengalami kerusakan berat',
        'Banyak vandalisme di fasilitas umum',
        'Area rawan banjir saat hujan deras',
    ];

    private array $firstNames = [
        'Ahmad', 'Rina', 'Budi', 'Dewi', 'Agus', 'Siti', 'Dodi', 'Maya',
        'Hendra', 'Fitri', 'Rudi', 'Nina', 'Bambang', 'Rani', 'Eko', 'Desi',
        'Heri', 'Yuli', 'Joko', 'Tini',
    ];

    private array $lastNames = [
        'Santoso', 'Wijaya', 'Kusuma', 'Putri', 'Pratama', 'Utami', 'Hidayat',
        'Ningsih', 'Saputra', 'Lestari', 'Ramadhani', 'Wulandari', 'Nugroho',
        'Handayani', 'Setiawan', 'Anggraini', 'Siregar', 'Nasution', 'Asmara',
    ];

    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $this->db->transStart();

        $this->createUsers($now);
        $this->createComplaints($now);
        $this->createUpvotes($now);

        $this->db->transComplete();

        echo '  - Total complaints: ' . number_format(2000) . "\n";
        echo '  - Total upvotes: ' . number_format(8000) . "\n";
    }

    private function createUsers(string $now): void
    {
        $existingCount = $this->db->table('users')->countAll();
        $needed = 20 - $existingCount;

        if ($needed <= 0) {
            echo '  - Users already 20 or more, skipping...' . "\n";
            return;
        }

        $password = password_hash('warga123', PASSWORD_DEFAULT);
        $values = [];

        for ($i = 0; $i < $needed; $i++) {
            $name = $this->firstNames[$i] . ' ' . $this->lastNames[$i];
            $email = strtolower(str_replace(' ', '.', $name)) . '@gmail.com';
            $phone = '08' . str_pad((string) mt_rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
            $role = ($i < 3) ? 'admin_instansi' : 'warga';

            // ensure unique email
            if (preg_match('/[^a-z0-9.]/', $email)) {
                $email = strtolower(str_replace(' ', '', $name)) . $i . '@gmail.com';
            }

            $values[] = "(" . $this->db->escape($name)
                . "," . $this->db->escape($email)
                . "," . $this->db->escape($phone)
                . "," . $this->db->escape($password)
                . "," . $this->db->escape($role)
                . "," . $this->db->escape($now)
                . "," . $this->db->escape($now) . ")";
        }

        $sql = "INSERT INTO users (name, email, phone, password, role, created_at, updated_at) VALUES " . implode(',', $values);
        $this->db->query($sql);

        echo '  - Added ' . $needed . ' users (total: 20)' . "\n";
    }

    private function createComplaints(string $now): void
    {
        $values = [];
        $totalComplaints = 2000;

        $statuses = ['pending', 'in_progress', 'resolved', 'rejected'];
        $statusWeights = [40, 25, 25, 10];

        for ($i = 0; $i < $totalComplaints; $i++) {
            $city = $this->indonesianCities[array_rand($this->indonesianCities)];
            $lat = $city['lat'] + (mt_rand(-100, 100) / 1000);
            $lng = $city['lng'] + (mt_rand(-100, 100) / 1000);

            $userId = mt_rand(1, 20);
            $categoryId = mt_rand(1, 6);

            $desc = $this->complaintDescriptions[array_rand($this->complaintDescriptions)];
            if (mt_rand(0, 1)) {
                $desc .= ' di wilayah ' . $city['city'];
            }

            $status = $this->weightedRandom($statuses, $statusWeights);
            $upvotes = ($status === 'resolved' || $status === 'in_progress')
                ? mt_rand(0, 50)
                : mt_rand(0, 20);
            $createdAt = $this->randomDateTime('-3 months', $now);

            $point = "ST_GeomFromText(" . $this->db->escape("POINT($lat $lng)") . ", 4326)";

            $values[] = "(" . (int) $userId
                . "," . (int) $categoryId
                . "," . $this->db->escape($desc)
                . ",NULL" // photo_before
                . ",NULL" // photo_after
                . "," . (float) $lat
                . "," . (float) $lng
                . "," . $point
                . "," . $this->db->escape($status)
                . "," . (int) $upvotes
                . "," . $this->db->escape($createdAt)
                . "," . $this->db->escape($createdAt) . ")";
        }

        $chunks = array_chunk($values, 500);
        foreach ($chunks as $chunk) {
            $sql = "INSERT INTO complaints (user_id, category_id, description, photo_before, photo_after, lat, lng, location, status, upvotes, created_at, updated_at)
                    VALUES " . implode(',', $chunk);
            $this->db->query($sql);
        }
    }

    private function createUpvotes(string $now): void
    {
        $values = [];
        $totalUpvotes = 8000;
        $usedPairs = [];

        while (count($usedPairs) < $totalUpvotes) {
            $userId = mt_rand(1, 20);
            $complaintId = mt_rand(1, 2000);
            $key = $userId . '-' . $complaintId;

            if (isset($usedPairs[$key])) {
                continue;
            }
            $usedPairs[$key] = true;

            $createdAt = $this->randomDateTime('-3 months', $now);

            $values[] = "(" . (int) $userId
                . "," . (int) $complaintId
                . "," . $this->db->escape($createdAt) . ")";
        }

        $chunks = array_chunk($values, 500);
        foreach ($chunks as $chunk) {
            $sql = "INSERT IGNORE INTO upvotes (user_id, complaint_id, created_at) VALUES " . implode(',', $chunk);
            $this->db->query($sql);
        }
    }

    private function weightedRandom(array $items, array $weights): string
    {
        $rand = mt_rand(1, 100);
        $cumulative = 0;

        foreach ($items as $i => $item) {
            $cumulative += $weights[$i];
            if ($rand <= $cumulative) {
                return $item;
            }
        }
        return $items[0];
    }

    private function randomDateTime(string $start, string $end): string
    {
        $startTs = strtotime($start);
        $endTs = strtotime($end);
        $randomTs = mt_rand($startTs, $endTs);
        return date('Y-m-d H:i:s', $randomTs);
    }
}
