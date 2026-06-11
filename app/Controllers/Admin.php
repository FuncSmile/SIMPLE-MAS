<?php

namespace App\Controllers;

use App\Models\ComplaintModel;
use App\Models\CategoryModel;
use App\Models\UserModel;

class Admin extends BaseController
{
    public function index(): string
    {
        $complaintModel = new ComplaintModel();
        $categoryModel  = new CategoryModel();

        $stats = [
            'total'        => $complaintModel->countAll(),
            'pending'      => $complaintModel->where('status', 'pending')->countAllResults(),
            'in_progress'  => $complaintModel->where('status', 'in_progress')->countAllResults(),
            'resolved'     => $complaintModel->where('status', 'resolved')->countAllResults(),
            'rejected'     => $complaintModel->where('status', 'rejected')->countAllResults(),
        ];

        $categories = $categoryModel->findAll();

        return view('layouts/header', ['title' => 'Panel Admin - SIMPEL-MAS'])
            . view('admin/index', [
                'stats'      => $stats,
                'categories' => $categories,
            ])
            . view('layouts/footer');
    }

    public function datatable(): string
    {
        $categoryModel = new CategoryModel();
        $categories    = $categoryModel->findAll();

        return view('layouts/header', ['title' => 'Data Pengaduan - SIMPEL-MAS'])
            . view('admin/datatable', ['categories' => $categories])
            . view('layouts/footer');
    }

    public function analytics(): string
    {
        return view('layouts/header', ['title' => 'Analitik - SIMPEL-MAS'])
            . view('admin/analytics')
            . view('layouts/footer');
    }

    public function users(): string
    {
        if (! $this->isSuperAdmin()) {
            return redirect()->to('/admin')->with('error', 'Akses hanya untuk Super Admin.');
        }

        return view('layouts/header', ['title' => 'Manajemen Pengguna - SIMPEL-MAS'])
            . view('admin/users')
            . view('layouts/footer');
    }

    public function categories(): string
    {
        if (! $this->isSuperAdmin()) {
            return redirect()->to('/admin')->with('error', 'Akses hanya untuk Super Admin.');
        }

        return view('layouts/header', ['title' => 'Manajemen Kategori - SIMPEL-MAS'])
            . view('admin/categories')
            . view('layouts/footer');
    }
}
