<div class="flex flex-col h-[calc(100vh-64px)]">
    <div class="bg-white shadow-sm border-b px-4 py-3 flex flex-wrap items-center justify-between gap-2">
        <h2 class="text-lg font-semibold text-gray-800">Peta Pengaduan Masyarakat</h2>
        <div class="flex items-center gap-3">
            <select id="filterCategory" class="text-sm border border-gray-300 rounded px-2 py-1">
                <option value="all">Semua Kategori</option>
                <?php
                $categories = model('App\Models\CategoryModel')->findAll();
                foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterStatus" class="text-sm border border-gray-300 rounded px-2 py-1">
                <option value="all">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="in_progress">Diproses</option>
                <option value="resolved">Selesai</option>
                <option value="rejected">Ditolak</option>
            </select>
            <a href="/complaint/create" class="bg-accent hover:bg-yellow-500 text-black text-sm font-semibold px-4 py-2 rounded transition">
                + Buat Aduan
            </a>
        </div>
    </div>
    <div id="map" class="flex-1 w-full"></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
const map = L.map('map').setView([-6.200000, 106.816666], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

const markers = L.markerClusterGroup({ chunkedLoading: true, maxClusterRadius: 50 });
const allComplaints = <?= json_encode($complaints ?? []) ?>;
const upvotedIds = <?= json_encode($upvotedComplaintIds ?? []) ?>;
const currentUserId = <?= json_encode(session()->get('user_id')) ?>;

const statusColors = {
    'pending': '#f59e0b',
    'in_progress': '#3b82f6',
    'resolved': '#10b981',
    'rejected': '#ef4444'
};

const statusLabels = {
    'pending': 'Pending',
    'in_progress': 'Diproses',
    'resolved': 'Selesai',
    'rejected': 'Ditolak'
};

function getMarkerIcon(status, categoryId) {
    const color = statusColors[status] || '#6b7280';
    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="
            background:${color};
            width:28px;height:28px;
            border-radius:50%;
            border:3px solid white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            cursor:pointer;
            "></div>`,
        iconSize: [28, 28],
        iconAnchor: [14, 14]
    });
}

function renderMarkers(complaints) {
    markers.clearLayers();
    complaints.forEach(function(c) {
        const icon = getMarkerIcon(c.status, c.category_id);
        const upvotedClass = upvotedIds.includes(parseInt(c.id)) ? 'text-blue-700' : 'text-gray-400';
        const m = L.marker([parseFloat(c.lat), parseFloat(c.lng)], { icon: icon });
        m.bindPopup(`
            <div style="min-width:220px">
                <h4 class="font-semibold text-sm mb-1">${c.category_name}</h4>
                <p class="text-xs text-gray-600 mb-2">${c.description.substring(0, 100)}...</p>
                <div class="flex items-center gap-2 text-xs mb-2">
                    <span class="px-2 py-0.5 rounded-full text-white text-xs" style="background:${statusColors[c.status]}">${statusLabels[c.status]}</span>
                    <span>&#128293; ${c.upvotes} upvote</span>
                </div>
                <a href="/complaint/${c.id}" class="text-blue-600 text-xs hover:underline font-semibold">Lihat Detail &rarr;</a>
            </div>
        `);
        markers.addLayer(m);
    });
    map.addLayer(markers);
}

function filterMarkers() {
    const catFilter = document.getElementById('filterCategory').value;
    const statusFilter = document.getElementById('filterStatus').value;

    const filtered = allComplaints.filter(function(c) {
        const catMatch = catFilter === 'all' || c.category_id == catFilter;
        const statusMatch = statusFilter === 'all' || c.status === statusFilter;
        return catMatch && statusMatch;
    });

    renderMarkers(filtered);
}

document.getElementById('filterCategory').addEventListener('change', filterMarkers);
document.getElementById('filterStatus').addEventListener('change', filterMarkers);

renderMarkers(allComplaints);

navigator.geolocation && navigator.geolocation.getCurrentPosition(function(pos) {
    map.setView([pos.coords.latitude, pos.coords.longitude], 14);
    L.circleMarker([pos.coords.latitude, pos.coords.longitude], {
        radius: 8, color: '#1e40af', fillColor: '#3b82f6', fillOpacity: 0.8
    }).addTo(map).bindPopup('<b>Lokasi Anda</b>').openPopup();
});
</script>
