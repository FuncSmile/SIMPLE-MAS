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
                    Foto Kondisi <span style="font-weight:400; color:#7a7a7a;">(maks. 2MB, akan dikompres otomatis)</span>
                </label>
                <input type="file" name="photo_before" id="photoInput" accept="image/*" required class="input-field" style="padding:8px 14px;">
                <div id="photoPreview" style="display:none; margin-top:8px; border:1px solid #e0e0e0; border-radius:8px; overflow:hidden; background:#fafafc;">
                    <img id="photoPreviewImg" style="width:100%; height:auto; display:block; max-height:300px; object-fit:contain; background:#f5f5f7;">
                    <div style="padding:8px 12px; font-size:12px; color:#7a7a7a; letter-spacing:-0.12px; display:flex; justify-content:space-between; align-items:center; border-top:1px solid #e0e0e0;">
                        <span id="photoSizeInfo"></span>
                        <span id="photoCompressInfo" style="color:#10b981; font-weight:600;"></span>
                    </div>
                </div>
                <p style="font-size:12px; color:#7a7a7a; letter-spacing:-0.12px; margin:6px 0 0;">
                    Unggah foto kondisi yang menunjukkan masalah. Foto akan dikompres otomatis agar cepat diunggah.
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

/* ── Image compression ── */
const MAX_SIZE_MB = 2;
const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;
const MAX_DIM = 1920;
const COMPRESS_QUALITY = 0.7;

function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(2) + ' MB';
}

function compressImage(file) {
    return new Promise((resolve, reject) => {
        if (!file.type.startsWith('image/')) {
            reject(new Error('File harus berupa gambar.'));
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
                let w = img.width, h = img.height;

                if (w > MAX_DIM || h > MAX_DIM) {
                    const ratio = Math.min(MAX_DIM / w, MAX_DIM / h);
                    w = Math.round(w * ratio);
                    h = Math.round(h * ratio);
                }

                const canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, 0, 0, w, h);

                canvas.toBlob(function (blob) {
                    if (!blob) {
                        reject(new Error('Gagal mengompres gambar.'));
                        return;
                    }
                    const compressedFile = new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });
                    resolve({ file: compressedFile, originalSize: file.size, compressedSize: blob.size });
                }, 'image/jpeg', COMPRESS_QUALITY);
            };
            img.onerror = function () { reject(new Error('Gagal memuat gambar.')); };
            img.src = e.target.result;
        };
        reader.onerror = function () { reject(new Error('Gagal membaca file.')); };
        reader.readAsDataURL(file);
    });
}

function updatePhotoPreview(fileUrl, originalSize, compressedSize) {
    const preview = document.getElementById('photoPreview');
    const img = document.getElementById('photoPreviewImg');
    const sizeInfo = document.getElementById('photoSizeInfo');
    const compressInfo = document.getElementById('photoCompressInfo');

    preview.style.display = 'block';
    img.src = fileUrl;

    sizeInfo.textContent = 'Ukuran asli: ' + formatBytes(originalSize);

    if (compressedSize) {
        const saved = originalSize - compressedSize;
        const pct = originalSize > 0 ? Math.round((1 - compressedSize / originalSize) * 100) : 0;
        compressInfo.textContent = 'Terkompres: ' + formatBytes(compressedSize) + ' (' + pct + '% lebih kecil)';
    } else {
        compressInfo.textContent = 'Ukuran: ' + formatBytes(originalSize);
    }
}

document.getElementById('photoInput').addEventListener('change', async function () {
    const file = this.files[0];
    const submitBtn = document.getElementById('submitBtn');
    const preview = document.getElementById('photoPreview');
    const compressInfo = document.getElementById('photoCompressInfo');

    preview.style.display = 'none';
    submitBtn.disabled = false;
    submitBtn.textContent = 'Kirim Pengaduan';

    if (!file) return;

    if (file.size > MAX_SIZE_BYTES) {
        alert('Ukuran foto maksimal ' + MAX_SIZE_MB + 'MB. File Anda: ' + formatBytes(file.size));
        this.value = '';
        return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Mengompres foto...';

    try {
        const result = await compressImage(file);
        const compressedFile = result.file;

        if (compressedFile.size > MAX_SIZE_BYTES) {
            alert('Foto setelah kompresi masih melebihi ' + MAX_SIZE_MB + 'MB. Silakan pilih foto dengan resolusi lebih rendah.');
            this.value = '';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Kirim Pengaduan';
            return;
        }

        const dt = new DataTransfer();
        dt.items.add(compressedFile);
        this.files = dt.files;

        const reader = new FileReader();
        reader.onload = function (e) {
            updatePhotoPreview(e.target.result, result.originalSize, result.compressedSize);
        };
        reader.readAsDataURL(compressedFile);

        submitBtn.disabled = false;
        submitBtn.textContent = 'Kirim Pengaduan';
    } catch (err) {
        alert(err.message || 'Gagal memproses foto.');
        this.value = '';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Kirim Pengaduan';
    }
});
</script>
