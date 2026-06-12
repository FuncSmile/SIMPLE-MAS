<?php
$c = $complaint;
$statusClass = [
    'pending'     => 'status-pending',
    'in_progress' => 'status-in_progress',
    'resolved'    => 'status-resolved',
    'rejected'    => 'status-rejected',
];
$statusLabels = [
    'pending'     => 'Pending',
    'in_progress' => 'Sedang Diproses',
    'resolved'    => 'Selesai',
    'rejected'    => 'Ditolak',
];
?>

<div style="background:#f5f5f7; min-height:calc(100vh - 44px); padding:48px 20px;">
    <div style="max-width:860px; margin:0 auto;">

        <!-- Back link -->
        <a href="/dashboard" style="color:#0066cc; font-size:14px; letter-spacing:-0.224px; text-decoration:none; display:inline-block; margin-bottom:24px;"
           onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
            &larr; Kembali ke Peta
        </a>

        <!-- Main card -->
        <div class="card-utility" style="margin-bottom:20px;">

            <!-- Header row -->
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:24px;">
                <h1 style="font-size:28px; font-weight:600; line-height:1.14; letter-spacing:0.196px; color:#1d1d1f; margin:0;">
                    Pengaduan #<?= $c['id'] ?>
                </h1>
                <span class="status-pill <?= $statusClass[$c['status']] ?? 'status-pending' ?>">
                    <?= $statusLabels[$c['status']] ?>
                </span>
            </div>

            <!-- Details grid -->
            <div class="grid md:grid-cols-2 gap-8 mb-8">
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div>
                        <p style="font-size:12px; font-weight:400; letter-spacing:-0.12px; color:#7a7a7a; margin:0 0 2px;">Kategori</p>
                        <p style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f; margin:0;"><?= esc($c['category_name']) ?></p>
                    </div>
                    <div>
                        <p style="font-size:12px; font-weight:400; letter-spacing:-0.12px; color:#7a7a7a; margin:0 0 2px;">Instansi</p>
                        <p style="font-size:17px; font-weight:400; letter-spacing:-0.374px; color:#1d1d1f; margin:0;"><?= esc($c['agency_name'] ?? '-') ?></p>
                    </div>
                    <div>
                        <p style="font-size:12px; font-weight:400; letter-spacing:-0.12px; color:#7a7a7a; margin:0 0 2px;">Pelapor</p>
                        <p style="font-size:17px; font-weight:400; letter-spacing:-0.374px; color:#1d1d1f; margin:0;"><?= esc($c['reporter_name']) ?></p>
                    </div>
                    <div>
                        <p style="font-size:12px; font-weight:400; letter-spacing:-0.12px; color:#7a7a7a; margin:0 0 2px;">Tanggal</p>
                        <p style="font-size:17px; font-weight:400; letter-spacing:-0.374px; color:#1d1d1f; margin:0;"><?= date('d M Y H:i', strtotime($c['created_at'])) ?></p>
                    </div>
                    <div>
                        <p style="font-size:12px; font-weight:400; letter-spacing:-0.12px; color:#7a7a7a; margin:0 0 2px;">Koordinat</p>
                        <p style="font-size:14px; font-weight:400; letter-spacing:-0.224px; color:#7a7a7a; margin:0;"><?= esc($c['lat']) ?>, <?= esc($c['lng']) ?></p>
                    </div>
                </div>
                <div>
                    <p style="font-size:12px; font-weight:400; letter-spacing:-0.12px; color:#7a7a7a; margin:0 0 8px;">Deskripsi</p>
                    <p style="font-size:17px; font-weight:400; line-height:1.47; letter-spacing:-0.374px; color:#1d1d1f; margin:0; white-space:pre-wrap;"><?= esc($c['description']) ?></p>
                </div>
            </div>

            <!-- Upvote row -->
            <div style="border-top:1px solid #e0e0e0; padding-top:20px; display:flex; align-items:center; gap:16px;">
                <button id="upvoteBtn"
                    onclick="doUpvote(<?= $c['id'] ?>)"
                    <?= $hasUpvoted ? 'disabled' : '' ?>
                    class="btn-pill-ghost"
                    style="<?= $hasUpvoted ? 'opacity:.5; cursor:default;' : '' ?> font-size:14px; letter-spacing:-0.224px; padding:8px 18px; display:flex; align-items:center; gap:6px;">
                    <span id="upvoteIcon">&#128077;</span>
                    <span id="upvoteCount"><?= $c['upvotes'] ?></span>&nbsp;Upvote
                </button>
                <span id="upvoteMsg" style="font-size:14px; letter-spacing:-0.224px;"></span>
            </div>
        </div>

        <!-- Photos card -->
        <div class="card-utility" style="margin-bottom:20px; padding:0; overflow:hidden;">
            <div class="grid md:grid-cols-2" style="border-top:none;">
                <!-- Before photo -->
                <div style="padding:24px; border-right:1px solid #e0e0e0;">
                    <p style="font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin:0 0 12px;">&#128247; Foto Sebelum</p>
                    <?php if ($c['photo_before']): ?>
                        <img src="/uploads/<?= esc($c['photo_before']) ?>" alt="Foto Sebelum"
                             style="width:100%; border-radius:8px;" class="product-shadow">
                    <?php else: ?>
                        <div style="background:#f5f5f7; border-radius:8px; height:160px; display:flex; align-items:center; justify-content:center;">
                            <span style="font-size:14px; color:#7a7a7a; letter-spacing:-0.224px;">Tidak ada foto</span>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- After photo -->
                <div style="padding:24px;">
                    <p style="font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin:0 0 12px;">&#128248; Foto Sesudah</p>
                    <?php if (!empty($c['photo_after'])): ?>
                        <img src="/uploads/<?= esc($c['photo_after']) ?>" alt="Foto Sesudah"
                             style="width:100%; border-radius:8px;" class="product-shadow">
                    <?php else: ?>
                        <div style="background:#f5f5f7; border-radius:8px; height:160px; display:flex; align-items:center; justify-content:center;">
                            <span style="font-size:14px; color:#7a7a7a; letter-spacing:-0.224px;">
                                <?= $c['status'] === 'resolved' ? 'Menunggu unggahan' : 'Belum selesai' ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Map card -->
        <div class="card-utility" style="padding:0; overflow:hidden; margin-bottom:20px;">
            <div id="detailMap" style="width:100%; height:240px;"></div>
        </div>

        <!-- Nearby complaints -->
        <?php if (!empty($nearbyComplaints)): ?>
        <div class="card-utility">
            <h3 style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f; margin:0 0 16px;">
                &#128204; Laporan Terdekat
            </h3>
            <div style="display:flex; flex-direction:column; gap:0;">
                <?php foreach ($nearbyComplaints as $n): ?>
                    <?php if ($n['id'] != $c['id']): ?>
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid #e0e0e0; gap:12px;">
                            <div>
                                <a href="/complaint/<?= $n['id'] ?>" style="color:#0066cc; font-size:17px; font-weight:600; letter-spacing:-0.374px; text-decoration:none;"
                                   onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                    #<?= $n['id'] ?> &mdash; <?= esc($n['category_name']) ?>
                                </a>
                                <p style="font-size:12px; color:#7a7a7a; letter-spacing:-0.12px; margin:2px 0 0;">
                                    ~<?= round($n['distance'] ?? 0) ?>m &bull; <?= esc($n['reporter_name']) ?>
                                </p>
                            </div>
                            <span class="status-pill <?= $statusClass[$n['status']] ?? 'status-pending' ?>">
                                <?= $statusLabels[$n['status']] ?>
                            </span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
<script>
const detailMap = L.map('detailMap').setView([<?= $c['lat'] ?>, <?= $c['lng'] ?>], 16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OSM', maxZoom: 19
}).addTo(detailMap);
L.marker([<?= $c['lat'] ?>, <?= $c['lng'] ?>]).addTo(detailMap)
    .bindPopup('<b><?= esc($c['category_name']) ?></b><br><?= esc(substr($c['description'], 0, 80)) ?>...').openPopup();

<?php foreach ($nearbyComplaints as $n): ?>
<?php if ($n['id'] != $c['id']): ?>
L.circleMarker([<?= $n['lat'] ?>, <?= $n['lng'] ?>], {
    radius: 6, color: '#0066cc', fillColor: '#0066cc', fillOpacity: 0.5
}).addTo(detailMap).bindPopup('<a href="/complaint/<?= $n['id'] ?>">#<?= $n['id'] ?> &mdash; <?= esc($n['category_name']) ?></a>');
<?php endif; ?>
<?php endforeach; ?>

var _csrfName     = '<?= csrf_token() ?>';
var _csrfCookieN  = '<?= config("Security")->cookieName ?? "csrf_cookie_name" ?>';
var _csrfFallback = '<?= csrf_hash() ?>';
function getCsrf() {
    try {
        var m = document.cookie.match(new RegExp('(?:^|; )' + _csrfCookieN.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)'));
        return m ? decodeURIComponent(m[1]) : _csrfFallback;
    } catch (e) { return _csrfFallback; }
}

function doUpvote(complaintId) {
    var btn = document.getElementById('upvoteBtn');
    var msg = document.getElementById('upvoteMsg');

    btn.disabled = true;
    msg.textContent = '';

    fetch('/api/complaint/upvote', {
        method:  'POST',
        headers: {
            'Content-Type':     'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'complaint_id=' + encodeURIComponent(complaintId)
            + '&' + encodeURIComponent(_csrfName) + '=' + encodeURIComponent(getCsrf())
    })
    .then(function (r) {
        if (!r.ok) throw new Error('HTTP_' + r.status);
        return r.json();
    })
    .then(function (data) {
        if (data.success) {
            document.getElementById('upvoteCount').textContent = data.upvotes;
            btn.style.opacity = '.5';
            btn.style.cursor  = 'default';
            msg.textContent   = 'Upvote berhasil!';
            msg.style.color   = '#10b981';
        } else {
            btn.disabled      = false;
            msg.textContent   = data.message || 'Gagal memberikan upvote.';
            msg.style.color   = '#ef4444';
        }
    })
    .catch(function (err) {
        btn.disabled    = false;
        msg.style.color = '#ef4444';
        if (err.message && err.message.indexOf('HTTP_401') !== -1) {
            msg.textContent = 'Silakan login untuk memberikan upvote.';
        } else if (err.message && err.message.indexOf('HTTP_') !== -1) {
            msg.textContent = 'Sesi habis, muat ulang halaman.';
        } else {
            msg.textContent = 'Terjadi kesalahan jaringan. Coba lagi.';
        }
        console.error('doUpvote error:', err);
    });
}
</script>
