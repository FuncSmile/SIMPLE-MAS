<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('layouts/header', ['title' => 'SIMPEL-MAS - Sistem Pengaduan Masyarakat'])
            . view('home')
            . view('layouts/footer');
    }
}
