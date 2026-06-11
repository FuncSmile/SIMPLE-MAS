<?php

namespace App\Models;

use CodeIgniter\Model;

class ComplaintModel extends Model
{
    protected $table            = 'complaints';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'category_id', 'description', 'photo_before',
        'photo_after', 'lat', 'lng', 'status', 'upvotes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function findNearby(float $lat, float $lng, float $radiusMeters = 50): array
    {
        $db = $this->db;

        return $db->table('complaints c')
            ->select('c.id, c.user_id, c.category_id, c.description, c.photo_before, c.photo_after, c.lat, c.lng, c.status, c.upvotes, c.created_at, c.updated_at, cat.name AS category_name, u.name AS reporter_name,
                ST_Distance_Sphere(c.location, ST_GeomFromText(' . $db->escape("POINT($lat $lng)") . ', 4326)) AS distance')
            ->join('categories cat', 'cat.id = c.category_id')
            ->join('users u', 'u.id = c.user_id')
            ->having('distance <', $radiusMeters)
            ->orderBy('distance', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function findAllWithDetails(): array
    {
        return $this->select('complaints.id, complaints.user_id, complaints.category_id, complaints.description, complaints.photo_before, complaints.photo_after, complaints.lat, complaints.lng, complaints.status, complaints.upvotes, complaints.created_at, complaints.updated_at, categories.name AS category_name, users.name AS reporter_name')
            ->join('categories', 'categories.id = complaints.category_id')
            ->join('users', 'users.id = complaints.user_id')
            ->orderBy('complaints.created_at', 'DESC')
            ->findAll();
    }

    public function findWithDetails(int $id): ?array
    {
        return $this->select('complaints.id, complaints.user_id, complaints.category_id, complaints.description, complaints.photo_before, complaints.photo_after, complaints.lat, complaints.lng, complaints.status, complaints.upvotes, complaints.created_at, complaints.updated_at, categories.name AS category_name, categories.agency AS agency_name, users.name AS reporter_name, users.email AS reporter_email')
            ->join('categories', 'categories.id = complaints.category_id')
            ->join('users', 'users.id = complaints.user_id')
            ->where('complaints.id', $id)
            ->first();
    }

    public function getServerSideData(array $params): array
    {
        $builder = $this->db->table('complaints c')
            ->select('c.id, c.description, c.status, c.upvotes, c.created_at, cat.name AS category_name, u.name AS reporter_name, c.lat, c.lng')
            ->join('categories cat', 'cat.id = c.category_id')
            ->join('users u', 'u.id = c.user_id');

        if (! empty($params['search']['value'])) {
            $search = $params['search']['value'];
            $builder->groupStart()
                ->like('c.description', $search)
                ->orLike('cat.name', $search)
                ->orLike('u.name', $search)
                ->orLike('c.status', $search)
                ->groupEnd();
        }

        if (! empty($params['status']) && $params['status'] !== 'all') {
            $builder->where('c.status', $params['status']);
        }

        if (! empty($params['category']) && $params['category'] !== 'all') {
            $builder->where('c.category_id', (int) $params['category']);
        }

        $totalFiltered = $builder->countAllResults(false);

        $orderColumnMap = [
            0 => 'c.id',
            1 => 'c.description',
            2 => 'cat.name',
            3 => 'u.name',
            4 => 'c.status',
            5 => 'c.upvotes',
            6 => 'c.created_at',
        ];

        $orderColumnIndex = $params['order'][0]['column'] ?? 5;
        $orderDir         = $params['order'][0]['dir'] ?? 'desc';
        $orderColumn      = $orderColumnMap[$orderColumnIndex] ?? 'c.created_at';

        $builder->orderBy($orderColumn, $orderDir);

        $start  = $params['start'] ?? 0;
        $length = $params['length'] ?? 10;
        $data   = $builder->limit($length, $start)->get()->getResultArray();

        return [
            'draw'            => (int) ($params['draw'] ?? 1),
            'recordsTotal'    => $this->countAll(),
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ];
    }

    public function getHeatmapData(): array
    {
        return $this->select('lat, lng, upvotes, status, category_id')
            ->findAll();
    }

    public function getCategoryStats(): array
    {
        return $this->db->table('complaints c')
            ->select('cat.name AS category, COUNT(*) AS total')
            ->join('categories cat', 'cat.id = c.category_id')
            ->groupBy('c.category_id')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getStatusStats(): array
    {
        return $this->db->table('complaints')
            ->select('status, COUNT(*) AS total')
            ->groupBy('status')
            ->get()
            ->getResultArray();
    }

    public function getTrendData(string $period = 'monthly'): array
    {
        $format = $period === 'monthly' ? '%Y-%m' : '%Y-%m-%d';
        return $this->db->table('complaints')
            ->select("DATE_FORMAT(created_at, '$format') AS label, COUNT(*) AS total")
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->limit(12)
            ->get()
            ->getResultArray();
    }

    public function insertComplaint(array $data): int
    {
        $db       = $this->db;
        $lat      = (float) $data['lat'];
        $lng      = (float) $data['lng'];
        $now      = date('Y-m-d H:i:s');
        $photo    = $data['photo_before'] ?? null;

        $sql = "INSERT INTO complaints (user_id, category_id, description, photo_before, lat, lng, location, status, upvotes, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ST_GeomFromText(?, 4326), 'pending', 0, ?, ?)";

        $db->query($sql, [
            $data['user_id'],
            $data['category_id'],
            $data['description'],
            $photo,
            $lat,
            $lng,
            "POINT($lat $lng)",
            $now,
            $now,
        ]);

        return $db->insertID();
    }
}
