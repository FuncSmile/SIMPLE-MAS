<?php

namespace App\Models;

use CodeIgniter\Model;

class UpvoteModel extends Model
{
    protected $table            = 'upvotes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'complaint_id'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    public function hasUpvoted(int $userId, int $complaintId): bool
    {
        return $this->where('user_id', $userId)
            ->where('complaint_id', $complaintId)
            ->countAllResults() > 0;
    }

    public function getUserUpvotedComplaintIds(int $userId): array
    {
        $rows = $this->select('complaint_id')->where('user_id', $userId)->findAll();
        return array_column($rows, 'complaint_id');
    }
}
