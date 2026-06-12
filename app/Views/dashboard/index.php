<?php $GLOBALS['ci_no_footer'] = true; ?>
<style>
.complaint-item { border-bottom:1px solid #f0f0f0; transition:background 0.12s; cursor:pointer; }
.complaint-item:hover { background:#f5f5f7; }
.complaint-item:last-child { border-bottom:none; }
.complaint-item.active { background:#f0f5ff; border-left:3px solid #0066cc; }
#complaint-panel::-webkit-scrollbar { width:4px; }
#complaint-panel::-webkit-scrollbar-track { background:#f5f5f7; }
#complaint-panel::-webkit-scrollbar-thumb { background:#cccccc; border-radius:2px; }
/* Leaflet popup override */
.leaflet-popup-content-wrapper { border-radius:12px !important; padding:0 !important; }
.leaflet-popup-content { margin:0 !important; }
</style>

<!-- ── FILTER STRIP ── -->
<div class="sub-nav-frosted">
    <div style="max-width:100%; padding:0 20px; display:flex; align-items:center; justify-content:space-between; height:52px; gap:12px;">
        <span style="font-size:17px; font-weight:600; line-height:1.19; letter-spacing:-0.374px; color:#1d1d1f; white-space:nowrap;">
            Peta Pengaduan
        </span>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <select id="filterCategory" class="input-field" style="width:auto; padding:6px 12px; font-size:14px; border-radius:9999px; min-width:140px;">
                <option value="all">Semua Kategori</option>
                <?php
                $categories = model('App\Models\CategoryModel')->findAll();
                foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterStatus" class="input-field" style="width:auto; padding:6px 12px; font-size:14px; border-radius:9999px; min-width:130px;">
                <option value="all">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="in_progress">Diproses</option>
                <option value="resolved">Selesai</option>
                <option value="rejected">Ditolak</option>
            </select>
            <a href="/complaint/create" class="btn-pill" style="font-size:14px; letter-spacing:-0.224px; padding:7px 16px; white-space:nowrap;">
                + Buat Aduan
            </a>
        </div>
    </div>
</div>

<!-- ── SPLIT LAYOUT: map left + list right ── -->
<div style="display:flex; height:calc(100vh - 96px); overflow:hidden;">

    <!-- Map: left, takes remaining space -->
    <div style="flex:1; min-width:0; position:relative;">
        <div id="map" style="height:100%; width:100%;"></div>
    </div>

    <!-- Hairline divider -->
    <div style="width:1px; background:#e0e0e0; flex-shrink:0;"></div>

    <!-- Complaint list: right sidebar, fixed 360px, scrollable -->
    <div id="complaint-panel" style="
        width:360px; flex-shrink:0;
        overflow-y:auto;
        background:#ffffff;
        display:flex; flex-direction:column;
    ">
        <!-- Sticky list header -->
        <div style="
            padding:14px 20px; border-bottom:1px solid #e0e0e0;
            position:sticky; top:0; background:#ffffff; z-index:5;
            display:flex; align-items:center; justify-content:space-between;
        ">
            <span style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f;">Pengaduan</span>
            <span id="complaint-count" style="font-size:12px; letter-spacing:-0.12px; color:#7a7a7a;"></span>
        </div>

        <!-- Dynamic list (rendered by JS) -->
        <div id="complaint-list" style="flex:1;"></div>
    </div>

</div>

<!-- Leaflet 1.9.4 JS — integrity hash verified from browser console -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
<!-- MarkerCluster (depends on Leaflet — must come after) -->
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js" crossorigin="anonymous"></script>

<script>
var map = L.map('map').setView([-6.200000, 106.816666], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

var markerGroup = L.markerClusterGroup({ chunkedLoading: true, maxClusterRadius: 50 });
var allComplaints = <?= json_encode($complaints ?? []) ?>;
var markerMap = {}; /* id -> L.marker, for focusComplaint() */

var statusColors = {
    'pending':     '#f59e0b',
    'in_progress': '#0066cc',
    'resolved':    '#10b981',
    'rejected':    '#ef4444'
};
var statusLabels = {
    'pending':     'Pending',
    'in_progress': 'Diproses',
    'resolved':    'Selesai',
    'rejected':    'Ditolak'
};

function getMarkerIcon(status) {
    var color = statusColors[status] || '#6b7280';
    return L.divIcon({
        className: '',
        html: '<div style="background:' + color + ';width:28px;height:28px;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.28);cursor:pointer;"></div>',
        iconSize: [28, 28],
        iconAnchor: [14, 14]
    });
}

/* ── Render markers + list ── */
function renderAll(complaints) {
    markerGroup.clearLayers();
    markerMap = {};

    complaints.forEach(function (c) {
        var m = L.marker([parseFloat(c.lat), parseFloat(c.lng)], { icon: getMarkerIcon(c.status) });
        var color = statusColors[c.status] || '#6b7280';
        m.bindPopup(
            '<div style="min-width:220px;font-family:system-ui,-apple-system,sans-serif;padding:14px 16px;">' +
                '<p style="font-size:14px;font-weight:600;color:#1d1d1f;margin:0 0 4px;letter-spacing:-0.224px;">' + (c.category_name || '-') + '</p>' +
                '<p style="font-size:12px;color:#7a7a7a;margin:0 0 10px;line-height:1.4;">' + (c.description || '').substring(0, 100) + '...</p>' +
                '<div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">' +
                    '<span style="background:' + color + ';color:#fff;font-size:11px;font-weight:600;padding:2px 10px;border-radius:9999px;">' + (statusLabels[c.status] || c.status) + '</span>' +
                    '<span style="font-size:12px;color:#7a7a7a;">&#128077; ' + (c.upvotes || 0) + '</span>' +
                '</div>' +
                '<a href="/complaint/' + c.id + '" style="color:#0066cc;font-size:13px;font-weight:600;text-decoration:none;" ' +
                   'onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">Lihat Detail &rarr;</a>' +
            '</div>'
        );
        markerGroup.addLayer(m);
        markerMap[c.id] = m;
    });

    map.addLayer(markerGroup);
    renderList(complaints);
}

/* ── Render right-panel list ── */
function renderList(complaints) {
    var countEl = document.getElementById('complaint-count');
    var listEl  = document.getElementById('complaint-list');

    countEl.textContent = complaints.length + ' laporan';

    if (complaints.length === 0) {
        listEl.innerHTML = '<div style="padding:40px 20px;text-align:center;color:#7a7a7a;font-size:14px;letter-spacing:-0.224px;">Tidak ada pengaduan ditemukan.</div>';
        return;
    }

    listEl.innerHTML = complaints.map(function (c) {
        var color = statusColors[c.status] || '#6b7280';
        var label = statusLabels[c.status] || c.status;
        var desc  = (c.description || '').substring(0, 80) + ((c.description || '').length > 80 ? '...' : '');
        return (
            '<div class="complaint-item" id="item-' + c.id + '"' +
                ' onclick="focusComplaint(' + c.id + ',' + c.lat + ',' + c.lng + ')"' +
                ' style="padding:14px 20px;display:flex;align-items:flex-start;gap:12px;">' +
                /* status dot */
                '<div style="flex-shrink:0;width:10px;height:10px;border-radius:50%;background:' + color + ';margin-top:4px;"></div>' +
                /* text block */
                '<div style="flex:1;min-width:0;">' +
                    '<div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;flex-wrap:wrap;">' +
                        '<span style="font-size:10px;font-weight:700;letter-spacing:0.4px;color:' + color + ';text-transform:uppercase;">' + label + '</span>' +
                        '<span style="font-size:11px;color:#7a7a7a;">&#128077; ' + (c.upvotes || 0) + '</span>' +
                    '</div>' +
                    '<p style="font-size:14px;font-weight:600;color:#1d1d1f;margin:0 0 3px;letter-spacing:-0.224px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + (c.category_name || '-') + '</p>' +
                    '<p style="font-size:12px;color:#7a7a7a;margin:0;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">' + desc + '</p>' +
                '</div>' +
                /* detail link — stopPropagation so it doesn't trigger focusComplaint */
                '<a href="/complaint/' + c.id + '"' +
                   ' onclick="event.stopPropagation()"' +
                   ' style="flex-shrink:0;color:#0066cc;font-size:11px;font-weight:600;text-decoration:none;letter-spacing:-0.12px;margin-top:2px;white-space:nowrap;"' +
                   ' onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">Detail &rarr;</a>' +
            '</div>'
        );
    }).join('');
}

/* ── Focus map on complaint + open popup ── */
function focusComplaint(id, lat, lng) {
    /* Highlight active item in list */
    document.querySelectorAll('.complaint-item.active').forEach(function (el) {
        el.classList.remove('active');
    });
    var itemEl = document.getElementById('item-' + id);
    if (itemEl) itemEl.classList.add('active');

    var m = markerMap[id];
    if (!m) {
        map.setView([parseFloat(lat), parseFloat(lng)], 16, { animate: true });
        return;
    }
    /* zoomToShowLayer unspiders the cluster if needed, then fires callback */
    markerGroup.zoomToShowLayer(m, function () { m.openPopup(); });
}

/* ── Filter ── */
function filterAll() {
    var catFilter    = document.getElementById('filterCategory').value;
    var statusFilter = document.getElementById('filterStatus').value;
    var filtered = allComplaints.filter(function (c) {
        return (catFilter    === 'all' || c.category_id == catFilter) &&
               (statusFilter === 'all' || c.status === statusFilter);
    });
    renderAll(filtered);
}

document.getElementById('filterCategory').addEventListener('change', filterAll);
document.getElementById('filterStatus').addEventListener('change', filterAll);

/* ── Initial render ── */
renderAll(allComplaints);

/* ── User location dot ── */
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (pos) {
        var lat = pos.coords.latitude, lng = pos.coords.longitude;
        map.setView([lat, lng], 14);
        L.circleMarker([lat, lng], {
            radius: 8, color: '#0066cc', fillColor: '#0066cc', fillOpacity: 0.8
        }).addTo(map).bindPopup('<b style="font-size:13px;">Lokasi Anda</b>').openPopup();
    });
}
</script>
