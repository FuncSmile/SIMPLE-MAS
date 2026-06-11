<?php

namespace App\Controllers;

use App\Models\ComplaintModel;
use App\Models\UpvoteModel;
use App\Models\UserModel;
use App\Models\CategoryModel;

class Api extends BaseController
{
    public function mapData()
    {
        $complaintModel = new ComplaintModel();
        $complaints     = $complaintModel->findAllWithDetails();

        return $this->response->setJSON(['data' => $complaints]);
    }

    public function heatmapData()
    {
        $complaintModel = new ComplaintModel();
        $data           = $complaintModel->getHeatmapData();

        return $this->response->setJSON(['data' => $data]);
    }

    public function chartData()
    {
        $complaintModel = new ComplaintModel();

        return $this->response->setJSON([
            'categories' => $complaintModel->getCategoryStats(),
            'statuses'   => $complaintModel->getStatusStats(),
            'trend'      => $complaintModel->getTrendData('monthly'),
        ]);
    }

    public function upvote()
    {
        if (! $this->isLoggedIn()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
        }

        $complaintId = (int) $this->request->getPost('complaint_id');
        $userId      = $this->getUserId();

        if ($userId === null || $complaintId <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak valid.']);
        }

        $upvoteModel    = new UpvoteModel();
        $complaintModel = new ComplaintModel();

        if ($upvoteModel->hasUpvoted($userId, $complaintId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anda sudah memberikan upvote.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $upvoteModel->insert([
            'user_id'      => $userId,
            'complaint_id' => $complaintId,
        ]);

        $complaintModel->where('id', $complaintId)
            ->set('upvotes', 'upvotes + 1', false)
            ->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memberikan upvote.']);
        }

        $complaint = $complaintModel->find($complaintId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Upvote berhasil!',
            'upvotes' => $complaint['upvotes'],
        ]);
    }

    public function nearby()
    {
        $lat = (float) $this->request->getPost('lat');
        $lng = (float) $this->request->getPost('lng');

        if (! $lat || ! $lng) {
            return $this->response->setJSON(['data' => []]);
        }

        $complaintModel = new ComplaintModel();
        $nearby         = $complaintModel->findNearby($lat, $lng, 50);

        return $this->response->setJSON(['data' => $nearby]);
    }

    public function updateStatus()
    {
        if (! $this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $complaintId = (int) $this->request->getPost('complaint_id');
        $status      = $this->request->getPost('status');

        $allowedStatuses = ['pending', 'in_progress', 'resolved', 'rejected'];
        if (! in_array($status, $allowedStatuses)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status tidak valid.']);
        }

        $complaintModel = new ComplaintModel();
        $complaintModel->update($complaintId, ['status' => $status]);

        return $this->response->setJSON(['success' => true, 'message' => 'Status berhasil diperbarui.']);
    }

    public function datatableData()
    {
        $params = $this->request->getGet();

        $complaintModel = new ComplaintModel();
        $result         = $complaintModel->getServerSideData($params);

        return $this->response->setJSON($result);
    }

    // User management (super admin only)
    public function createUser()
    {
        if (! $this->isSuperAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $userModel = new UserModel();
        $userModel->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'phone'    => $this->request->getPost('phone'),
            'password' => $this->request->getPost('password'),
            'role'     => $this->request->getPost('role'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil dibuat.']);
    }

    public function updateUser()
    {
        if (! $this->isSuperAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $userModel = new UserModel();
        $id        = (int) $this->request->getPost('id');
        $data      = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'role'  => $this->request->getPost('role'),
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }

        $userModel->update($id, $data);

        return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil diperbarui.']);
    }

    public function deleteUser()
    {
        if (! $this->isSuperAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $userModel = new UserModel();
        $userModel->delete((int) $this->request->getPost('id'));

        return $this->response->setJSON(['success' => true, 'message' => 'Pengguna berhasil dihapus.']);
    }

    public function createCategory()
    {
        if (! $this->isSuperAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $categoryModel = new CategoryModel();
        $categoryModel->insert([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'agency'      => $this->request->getPost('agency'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Kategori berhasil dibuat.']);
    }

    public function updateCategory()
    {
        if (! $this->isSuperAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $categoryModel = new CategoryModel();
        $categoryModel->update((int) $this->request->getPost('id'), [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'agency'      => $this->request->getPost('agency'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Kategori berhasil diperbarui.']);
    }

    public function deleteCategory()
    {
        if (! $this->isSuperAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $categoryModel = new CategoryModel();
        $categoryModel->delete((int) $this->request->getPost('id'));

        return $this->response->setJSON(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
    }

    public function listUsers()
    {
        if (! $this->isSuperAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
        }

        $userModel = new UserModel();
        $users     = $userModel->findAll();

        return $this->response->setJSON(['data' => $users]);
    }

    public function listCategories()
    {
        $categoryModel = new CategoryModel();
        $categories    = $categoryModel->findAll();

        return $this->response->setJSON(['data' => $categories]);
    }
}
