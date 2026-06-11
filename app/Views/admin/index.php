<div class="max-w-7xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Panel Admin</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-3xl font-bold text-gray-800"><?= $stats['total'] ?></p>
            <p class="text-sm text-gray-500">Total Laporan</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-3xl font-bold text-yellow-600"><?= $stats['pending'] ?></p>
            <p class="text-sm text-gray-500">Pending</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-3xl font-bold text-blue-600"><?= $stats['in_progress'] ?></p>
            <p class="text-sm text-gray-500">Diproses</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-3xl font-bold text-green-600"><?= $stats['resolved'] ?></p>
            <p class="text-sm text-gray-500">Selesai</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-3xl font-bold text-red-600"><?= $stats['rejected'] ?></p>
            <p class="text-sm text-gray-500">Ditolak</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid md:grid-cols-3 gap-6">
        <a href="/admin/datatable" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition border-l-4 border-primary">
            <h3 class="font-semibold text-gray-800 text-lg mb-1">&#128203; Data Pengaduan</h3>
            <p class="text-sm text-gray-500">Lihat dan kelola semua laporan. Update status, filter, dan cari data.</p>
        </a>
        <a href="/admin/analytics" class="bg-white shadow rounded-lg p-6 hover:shadow-md transition border-l-4 border-green-500">
            <h3 class="font-semibold text-gray-800 text-lg mb-1">&#128202; Analitik</h3>
            <p class="text-sm text-gray-500">Heatmap wilayah, grafik tren kategori, dan persentase penyelesaian.</p>
        </a>
        <?php if (session()->get('role') === 'super_admin'): ?>
        <div class="space-y-4">
            <a href="/admin/users" class="bg-white shadow rounded-lg p-4 hover:shadow-md transition border-l-4 border-purple-500 block">
                <h3 class="font-semibold text-gray-800 mb-1">&#128101; Manajemen Pengguna</h3>
                <p class="text-xs text-gray-500">Kelola akun warga, admin, dan super admin.</p>
            </a>
            <a href="/admin/categories" class="bg-white shadow rounded-lg p-4 hover:shadow-md transition border-l-4 border-orange-500 block">
                <h3 class="font-semibold text-gray-800 mb-1">&#128230; Manajemen Kategori</h3>
                <p class="text-xs text-gray-500">Kelola kategori pengaduan dan instansi penanggung jawab.</p>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
