<?php

namespace App\Controllers;

use App\Models\ComplaintModel;
use App\Models\UpvoteModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $complaintModel = new ComplaintModel();
        $upvoteModel    = new UpvoteModel();

        $complaints        = $complaintModel->findAllWithDetails();
        $upvotedComplaintIds = $this->isLoggedIn()
            ? $upvoteModel->getUserUpvotedComplaintIds($this->getUserId())
            : [];

        return view('layouts/header', ['title' => 'Dashboard Peta - SIMPEL-MAS'])
            . view('dashboard/index', [
                'complaints'           => $complaints,
                'upvotedComplaintIds'  => $upvotedComplaintIds,
            ])
            . view('layouts/footer');
    }
}
