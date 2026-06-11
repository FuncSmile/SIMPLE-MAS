<div class="max-w-2xl mx-auto py-8 px-4">
    <h2 class="text-2xl font-bold text-gray-800 mb-2">Buat Pengaduan Baru</h2>
    <p class="text-gray-600 mb-6">Isi detail laporan Anda. Lokasi akan terdeteksi otomatis.</p>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-error">
            <ul class="list-disc pl-5">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div id="duplicateWarning" class="alert alert-warning hidden mb-4"></div>

    <form id="complaintForm" action="/complaint/store" method="post" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6 space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="lat" id="lat">
        <input type="hidden" name="lng" id="lng">

        <div id="locationStatus" class="text-sm text-gray-500 bg-gray-100 rounded px-3 py-2 flex items-center gap-2">
            <span class="animate-pulse">&#128204;</span> Mendeteksi lokasi Anda...
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <select name="category_id" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>>
                        <?= esc($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Masalah</label>
            <textarea name="description" required rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                placeholder="Jelaskan masalah yang Anda temukan secara detail..."><?= old('description') ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Kondisi (maks. 5MB)</label>
            <input type="file" name="photo_before" accept="image/*" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
            <p class="text-xs text-gray-500 mt-1">Unggah foto kondisi yang menunjukkan masalah.</p>
        </div>

        <div id="mapPreview" class="w-full h-48 rounded-lg border border-gray-300 mb-2"></div>

        <button type="submit" id="submitBtn" class="w-full bg-primary hover:bg-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition">
            Kirim Pengaduan
        </button>
    </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let userLat = null, userLng = null;
const previewMap = L.map('mapPreview').setView([-6.200000, 106.816666], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OSM',
    maxZoom: 19
}).addTo(previewMap);
let locationMarker;

function setLocation(lat, lng) {
    userLat = lat; userLng = lng;
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;
    document.getElementById('locationStatus').innerHTML = '<span class="text-green-600 font-semibold">&#9989;</span> Lokasi terdeteksi: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);

    if (locationMarker) previewMap.removeLayer(locationMarker);
    locationMarker = L.marker([lat, lng]).addTo(previewMap).bindPopup('Lokasi Laporan').openPopup();
    previewMap.setView([lat, lng], 16);

    checkNearby(lat, lng);
}

function checkNearby(lat, lng) {
    fetch('/complaint/check-duplicate?lat=' + lat + '&lng=' + lng)
        .then(r => r.json())
        .then(data => {
            if (data.duplicates && data.duplicates.length > 0) {
                let html = '<strong>&#9888; Laporan Serupa Terdeteksi!</strong><br>Ada ' + data.duplicates.length + ' laporan dalam radius 50m:<ul class="list-disc pl-5 mt-1">';
                data.duplicates.forEach(function(d) {
                    html += '<li><a href="/complaint/' + d.id + '" class="text-blue-700 underline">' + d.category_name + '</a> - ' + (d.distance ? Math.round(d.distance) + 'm' : 'dekat') + '</li>';
                });
                html += '</ul><p class="mt-2 text-sm">Disarankan memberi <strong>upvote</strong> pada laporan yang sudah ada.</p>';
                document.getElementById('duplicateWarning').innerHTML = html;
                document.getElementById('duplicateWarning').classList.remove('hidden');
            } else {
                document.getElementById('duplicateWarning').classList.add('hidden');
            }
        });
}

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
        setLocation(pos.coords.latitude, pos.coords.longitude);
    }, function(err) {
        document.getElementById('locationStatus').innerHTML = '<span class="text-red-600">&#10060;</span> Gagal mendeteksi lokasi. Silakan izinkan akses lokasi.';
    });
} else {
    document.getElementById('locationStatus').innerHTML = '<span class="text-red-600">&#10060;</span> Browser tidak mendukung geolocation.';
}

previewMap.on('click', function(e) {
    setLocation(e.latlng.lat, e.latlng.lng);
});
</script>
