<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'SIMPEL-MAS') ?></title>
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#3b82f6',
                        accent: '#f59e0b',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .alert { padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1rem; }
        .alert-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-info { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .alert-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<nav class="bg-primary text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-2">
                <a href="/" class="text-xl font-bold tracking-tight">SIMPEL-MAS</a>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <a href="/" class="hover:text-accent transition px-2 py-1">Beranda</a>
                <?php $session = service('session'); if ($session->get('isLoggedIn')): ?>
                    <?php if ($session->get('role') === 'warga'): ?>
                        <a href="/dashboard" class="hover:text-accent transition px-2 py-1">Peta</a>
                        <a href="/complaint/create" class="hover:text-accent transition px-2 py-1">Buat Aduan</a>
                    <?php endif; ?>
                    <?php if (in_array($session->get('role'), ['admin_instansi', 'super_admin'])): ?>
                        <a href="/admin" class="hover:text-accent transition px-2 py-1">Panel Admin</a>
                    <?php endif; ?>
                    <span class="text-gray-300">|</span>
                    <span class="text-sm text-gray-200"><?= esc($session->get('name')) ?></span>
                    <a href="/logout" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm transition">Logout</a>
                <?php else: ?>
                    <a href="/login" class="hover:text-accent transition px-2 py-1">Login</a>
                    <a href="/register" class="bg-accent hover:bg-yellow-500 text-black px-3 py-1 rounded text-sm font-semibold transition">Daftar</a>
                <?php endif; ?>
            </div>
            <div class="md:hidden">
                <button id="mobileMenuBtn" class="text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="mobileMenu" class="hidden md:hidden pb-4 space-y-2">
            <a href="/" class="block text-white py-2">Beranda</a>
            <?php if ($session->get('isLoggedIn')): ?>
                <?php if ($session->get('role') === 'warga'): ?>
                    <a href="/dashboard" class="block text-white py-2">Peta</a>
                    <a href="/complaint/create" class="block text-white py-2">Buat Aduan</a>
                <?php endif; ?>
                <?php if (in_array($session->get('role'), ['admin_instansi', 'super_admin'])): ?>
                    <a href="/admin" class="block text-white py-2">Panel Admin</a>
                <?php endif; ?>
                <a href="/logout" class="block text-red-300 py-2">Logout</a>
            <?php else: ?>
                <a href="/login" class="block text-white py-2">Login</a>
                <a href="/register" class="block text-white py-2">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
document.getElementById('mobileMenuBtn').addEventListener('click', function() {
    document.getElementById('mobileMenu').classList.toggle('hidden');
});
</script>

<main class="flex-1">
<?php if ($flash = session()->getFlashdata('success')): ?>
    <div class="max-w-7xl mx-auto px-4 mt-4"><div class="alert alert-success"><?= esc($flash) ?></div></div>
<?php endif; ?>
<?php if ($flash = session()->getFlashdata('error')): ?>
    <div class="max-w-7xl mx-auto px-4 mt-4"><div class="alert alert-error"><?= esc($flash) ?></div></div>
<?php endif; ?>
<?php if ($flash = session()->getFlashdata('warning')): ?>
    <div class="max-w-7xl mx-auto px-4 mt-4"><div class="alert alert-warning"><?= esc($flash) ?></div></div>
<?php endif; ?>
