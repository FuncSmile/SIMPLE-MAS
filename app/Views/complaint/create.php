<!-- ── CREATE COMPLAINT ── -->
<div style="background:#f5f5f7; min-height:calc(100vh - 44px); padding:48px 20px;">
    <div style="max-width:640px; margin:0 auto;">

        <h1 style="font-size:34px; font-weight:600; line-height:1.47; letter-spacing:-0.374px; color:#1d1d1f; margin:0 0 8px;">
            Buat Pengaduan
        </h1>
        <p style="font-size:17px; font-weight:400; line-height:1.47; letter-spacing:-0.374px; color:#7a7a7a; margin:0 0 32px;">
            Isi detail laporan Anda. Lokasi akan terdeteksi otomatis.
        </p>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error" style="margin-bottom:20px;">
                <ul style="margin:0; padding-left:20px;">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Duplicate warning -->
        <div id="duplicateWarning" class="alert alert-warning" style="display:none; margin-bottom:20px;"></div>

        <form id="complaintForm" action="/complaint/store" method="post" enctype="multipart/form-data"
              class="card-utility" style="display:flex; flex-direction:column; gap:20px;">
            <?= csrf_field() ?>
            <input type="hidden" name="lat" id="lat">
            <input type="hidden" name="lng" id="lng">

            <!-- Location status indicator -->
            <div id="locationStatus"
                 style="font-size:14px; letter-spacing:-0.224px; color:#7a7a7a; background:#f5f5f7; border:1px solid #e0e0e0; border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:8px;">
                <span style="animation:pulse 1.5s infinite;">&#128204;</span> Mendeteksi lokasi Anda...
            </div>

            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">
                    Kategori
                </label>
                <select name="category_id" required class="input-field">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">
                    Deskripsi Masalah
                </label>
                <textarea name="description" required rows="4" class="input-field"
                          style="resize:vertical; min-height:100px;"
                          placeholder="Jelaskan masalah yang Anda temukan secara detail..."><?= old('description') ?></textarea>
            </div>

            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">
                    Foto Kondisi <span style="font-weight:400; color:#7a7a7a;">(maks. 5MB)</span>
                </label>
                <input type="file" name="photo_before" accept="image/*" required class="input-field" style="padding:8px 14px;">
                <p style="font-size:12px; color:#7a7a7a; letter-spacing:-0.12px; margin:6px 0 0;">
                    Unggah foto kondisi yang menunjukkan masalah.
                </p>
            </div>

            <!-- Map preview -->
            <div id="mapPreview" style="width:100%; height:180px; border-radius:8px; border:1px solid #e0e0e0; overflow:hidden;"></div>

            <button type="submit" id="submitBtn" class="btn-pill" style="width:100%; text-align:center;">
                Kirim Pengaduan
            </button>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
<script>
let userLat = null, userLng = null;
const previewMap = L.map('mapPreview').setView([-6.200000, 106.816666], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OSM', maxZoom: 19
}).addTo(previewMap);
let locationMarker;

function setLocation(lat, lng) {
    userLat = lat; userLng = lng;
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
    document.getElementById('locationStatus').innerHTML =
        '<span style="color:#10b981;">&#9989;</span> <span style="color:#1d1d1f;">Lokasi terdeteksi: ' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '</span>';

    if (locationMarker) previewMap.removeLayer(locationMarker);
    locationMarker = L.marker([lat, lng]).addTo(previewMap).bindPopup('Lokasi Laporan').openPopup();
    previewMap.setView([lat, lng], 16);
    checkNearby(lat, lng);
}

function checkNearby(lat, lng) {
    fetch('/complaint/check-duplicate?lat=' + lat + '&lng=' + lng)
        .then(r => r.json())
        .then(data => {
            const el = document.getElementById('duplicateWarning');
            if (data.duplicates && data.duplicates.length > 0) {
                let html = '<strong>&#9888; Laporan Serupa Terdeteksi!</strong><br>Ada ' + data.duplicates.length + ' laporan dalam radius 50m:<ul style="margin:6px 0 0;padding-left:20px;">';
                data.duplicates.forEach(function(d) {
                    html += '<li><a href="/complaint/' + d.id + '" style="color:#0066cc;">' + d.category_name + '</a> &mdash; ' + (d.distance ? Math.round(d.distance) + 'm' : 'dekat') + '</li>';
                });
                html += '</ul><p style="margin:8px 0 0;font-size:14px;">Disarankan memberi <strong>upvote</strong> pada laporan yang sudah ada.</p>';
                el.innerHTML = html;
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        });
}

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
        setLocation(pos.coords.latitude, pos.coords.longitude);
    }, function() {
        document.getElementById('locationStatus').innerHTML =
            '<span style="color:#ef4444;">&#10060;</span> <span style="color:#1d1d1f;">Gagal mendeteksi lokasi. Silakan izinkan akses lokasi.</span>';
    });
} else {
    document.getElementById('locationStatus').innerHTML =
        '<span style="color:#ef4444;">&#10060;</span> <span style="color:#1d1d1f;">Browser tidak mendukung geolocation.</span>';
}

previewMap.on('click', function(e) { setLocation(e.latlng.lat, e.latlng.lng); });
</script>
