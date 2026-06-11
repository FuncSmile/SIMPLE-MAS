<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ComplaintModel;
use App\Models\UpvoteModel;

class Complaint extends BaseController
{
    public function create(): string
    {
        $categoryModel = new CategoryModel();
        $categories    = $categoryModel->findAll();

        return view('layouts/header', ['title' => 'Buat Pengaduan - SIMPEL-MAS'])
            . view('complaint/create', ['categories' => $categories])
            . view('layouts/footer');
    }

    public function store()
    {
        $rules = [
            'category_id' => 'required|integer',
            'description' => 'required|min_length[10]',
            'lat'         => 'required|numeric',
            'lng'         => 'required|numeric',
            'photo_before' => 'uploaded[photo_before]|max_size[photo_before,5120]|is_image[photo_before]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $photoBefore = $this->request->getFile('photo_before');
        $photoPath   = null;

        if ($photoBefore && $photoBefore->isValid() && ! $photoBefore->hasMoved()) {
            $newName = $photoBefore->getRandomName();
            $photoBefore->move(ROOTPATH . 'public/uploads', $newName);
            $photoPath = $newName;
        }

        $complaintModel = new ComplaintModel();
        $data           = [
            'user_id'      => $this->getUserId(),
            'category_id'  => $this->request->getPost('category_id'),
            'description'  => $this->request->getPost('description'),
            'lat'          => $this->request->getPost('lat'),
            'lng'          => $this->request->getPost('lng'),
            'photo_before' => $photoPath,
        ];

        $id = $complaintModel->insertComplaint($data);

        if (! $id) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengaduan.');
        }

        return redirect()->to('/complaint/' . $id)->with('success', 'Pengaduan berhasil dibuat!');
    }

    public function checkDuplicate()
    {
        $lat = $this->request->getGet('lat');
        $lng = $this->request->getGet('lng');

        if (! $lat || ! $lng) {
            return $this->response->setJSON(['duplicates' => []]);
        }

        $complaintModel = new ComplaintModel();
        $nearby         = $complaintModel->findNearby((float) $lat, (float) $lng, 50);

        return $this->response->setJSON(['duplicates' => $nearby]);
    }

    public function detail(int $id): string
    {
        $complaintModel = new ComplaintModel();
        $upvoteModel    = new UpvoteModel();

        $complaint = $complaintModel->findWithDetails($id);

        if (! $complaint) {
            return redirect()->to('/dashboard')->with('error', 'Pengaduan tidak ditemukan.');
        }

        $nearbyComplaints = $complaintModel->findNearby(
            (float) $complaint['lat'],
            (float) $complaint['lng'],
            100
        );

        $hasUpvoted = $this->isLoggedIn()
            ? $upvoteModel->hasUpvoted($this->getUserId(), $id)
            : false;

        return view('layouts/header', ['title' => 'Detail Pengaduan #' . $id . ' - SIMPEL-MAS'])
            . view('complaint/detail', [
                'complaint'        => $complaint,
                'hasUpvoted'       => $hasUpvoted,
                'nearbyComplaints' => $nearbyComplaints,
            ])
            . view('layouts/footer');
    }
}
