<?php
$c = $complaint;
$statusColors = [
    'pending'     => 'bg-yellow-100 text-yellow-800',
    'in_progress' => 'bg-blue-100 text-blue-800',
    'resolved'    => 'bg-green-100 text-green-800',
    'rejected'    => 'bg-red-100 text-red-800',
];
$statusLabels = [
    'pending'     => 'Pending',
    'in_progress' => 'Sedang Diproses',
    'resolved'    => 'Selesai',
    'rejected'    => 'Ditolak',
];
?>

<div class="max-w-4xl mx-auto py-8 px-4">
    <a href="/dashboard" class="text-primary hover:underline text-sm mb-4 inline-block">&larr; Kembali ke Peta</a>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <h2 class="text-xl font-bold text-gray-800">Pengaduan #<?= $c['id'] ?></h2>
                <span class="px-3 py-1 rounded-full text-sm font-semibold <?= $statusColors[$c['status']] ?>">
                    <?= $statusLabels[$c['status']] ?>
                </span>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <span class="text-sm text-gray-500">Kategori</span>
                        <p class="font-semibold text-gray-800"><?= esc($c['category_name']) ?></p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Instansi</span>
                        <p class="text-gray-700"><?= esc($c['agency_name'] ?? '-') ?></p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Pelapor</span>
                        <p class="text-gray-700"><?= esc($c['reporter_name']) ?></p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Tanggal</span>
                        <p class="text-gray-700"><?= date('d M Y H:i', strtotime($c['created_at'])) ?></p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Lokasi</span>
                        <p class="text-gray-700"><?= esc($c['lat']) ?>, <?= esc($c['lng']) ?></p>
                    </div>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Deskripsi</span>
                    <p class="text-gray-800 mt-1 whitespace-pre-wrap"><?= esc($c['description']) ?></p>
                </div>
            </div>

            <!-- Upvote -->
            <div class="mt-6 pt-4 border-t flex items-center gap-4">
                <button id="upvoteBtn"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition <?= $hasUpvoted ? 'bg-blue-100 text-blue-700 cursor-default' : 'bg-gray-100 hover:bg-blue-50 text-gray-700 hover:text-blue-700' ?>"
                    <?= $hasUpvoted ? 'disabled' : '' ?>
                    onclick="doUpvote(<?= $c['id'] ?>)">
                    <span id="upvoteIcon">&#128077;</span>
                    <span id="upvoteCount"><?= $c['upvotes'] ?></span> Upvote
                </button>
                <span id="upvoteMsg" class="text-sm"></span>
            </div>
        </div>

        <!-- Before / After Photos -->
        <div class="border-t">
            <div class="grid md:grid-cols-2 divide-x">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-700 mb-3">&#128247; Foto Sebelum</h3>
                    <?php if ($c['photo_before']): ?>
                        <img src="/uploads/<?= esc($c['photo_before']) ?>" alt="Foto Sebelum" class="w-full rounded-lg shadow-sm">
                    <?php else: ?>
                        <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center text-gray-400">Tidak ada foto</div>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <h3 class="font-semibold text-gray-700 mb-3">&#128248; Foto Sesudah</h3>
                    <?php if (! empty($c['photo_after'])): ?>
                        <img src="/uploads/<?= esc($c['photo_after']) ?>" alt="Foto Sesudah" class="w-full rounded-lg shadow-sm">
                    <?php else: ?>
                        <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center text-gray-400">
                            <?= $c['status'] === 'resolved' ? 'Menunggu unggahan' : 'Belum selesai' ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden">
        <div id="detailMap" class="w-full h-64"></div>
    </div>

    <!-- Nearby Complaints -->
    <?php if (! empty($nearbyComplaints)): ?>
        <div class="mt-6 bg-white shadow-md rounded-lg p-6">
            <h3 class="font-semibold text-gray-800 mb-4">&#128204; Laporan Terdekat</h3>
            <div class="space-y-3">
                <?php foreach ($nearbyComplaints as $n): ?>
                    <?php if ($n['id'] != $c['id']): ?>
                        <div class="flex items-center justify-between border-b pb-2">
                            <div>
                                <a href="/complaint/<?= $n['id'] ?>" class="text-primary hover:underline font-semibold">#<?= $n['id'] ?> - <?= esc($n['category_name']) ?></a>
                                <p class="text-xs text-gray-500">~<?= round($n['distance'] ?? 0) ?>m &bull; <?= esc($n['reporter_name']) ?></p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $statusColors[$n['status']] ?>"><?= $statusLabels[$n['status']] ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const detailMap = L.map('detailMap').setView([<?= $c['lat'] ?>, <?= $c['lng'] ?>], 16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OSM',
    maxZoom: 19
}).addTo(detailMap);
L.marker([<?= $c['lat'] ?>, <?= $c['lng'] ?>]).addTo(detailMap)
    .bindPopup('<b><?= esc($c['category_name']) ?></b><br><?= esc(substr($c['description'], 0, 80)) ?>...').openPopup();

<?php foreach ($nearbyComplaints as $n): ?>
<?php if ($n['id'] != $c['id']): ?>
L.circleMarker([<?= $n['lat'] ?>, <?= $n['lng'] ?>], {
    radius: 6, color: '#3b82f6', fillColor: '#3b82f6', fillOpacity: 0.5
}).addTo(detailMap).bindPopup('<a href="/complaint/<?= $n['id'] ?>">#<?= $n['id'] ?> - <?= esc($n['category_name']) ?></a>');
<?php endif; ?>
<?php endforeach; ?>

function doUpvote(complaintId) {
    fetch('/api/complaint/upvote', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'complaint_id=' + complaintId + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('upvoteCount').textContent = data.upvotes;
            document.getElementById('upvoteBtn').classList.add('bg-blue-100', 'text-blue-700', 'cursor-default');
            document.getElementById('upvoteBtn').classList.remove('hover:bg-blue-50');
            document.getElementById('upvoteBtn').disabled = true;
            document.getElementById('upvoteMsg').textContent = 'Upvote berhasil!';
            document.getElementById('upvoteMsg').className = 'text-sm text-green-600';
        } else {
            document.getElementById('upvoteMsg').textContent = data.message;
            document.getElementById('upvoteMsg').className = 'text-sm text-red-600';
        }
    });
}
</script>
