<?php $GLOBALS['ci_no_footer'] = true; ?>
<style>
/* Admin dashboard page styles */
.kpi-card { background:#fff; border:1px solid #e0e0e0; border-radius:14px; padding:20px 22px; }
.kpi-num  { font-size:34px; font-weight:700; line-height:1.1; letter-spacing:-0.5px; margin:0 0 4px; }
.kpi-label{ font-size:12px; letter-spacing:-0.12px; color:#7a7a7a; margin:0; }
.section-card { background:#fff; border:1px solid #e0e0e0; border-radius:18px; overflow:hidden; }
/* Leaflet popup */
.leaflet-popup-content-wrapper { border-radius:12px !important; padding:0 !important; }
.leaflet-popup-content { margin:0 !important; }
/* Complaint list in map panel */
.top-item { padding:12px 0; border-bottom:1px solid #f0f0f0; display:flex; align-items:flex-start; gap:10px; cursor:pointer; transition:background 0.12s; border-radius:6px; }
.top-item:hover { background:#f5f5f7; padding:12px 8px; margin:0 -8px; }
.top-item:last-child { border-bottom:none; }
</style>

<!-- ── ADMIN SHELL: sidebar + scrollable main ── -->
<div style="display:flex; height:calc(100vh - 44px); overflow:hidden;">

    <!-- Sidebar (shared partial) -->
    <?= view('admin/sidebar', ['currentPage' => 'dashboard']) ?>

    <!-- ── MAIN CONTENT ── -->
    <div style="flex:1; min-width:0; overflow-y:auto; background:#f5f5f7;">
        <div style="max-width:1280px; margin:0 auto; padding:32px 32px 56px;">

            <!-- Page header -->
            <div style="display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:28px; flex-wrap:wrap; gap:12px;">
                <div>
                    <p style="font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#0066cc; margin:0 0 4px;">SIMPEL-MAS</p>
                    <h1 style="font-size:34px; font-weight:700; line-height:1.1; letter-spacing:-0.5px; color:#1d1d1f; margin:0;">Dashboard</h1>
                </div>
                <p style="font-size:14px; color:#7a7a7a; letter-spacing:-0.224px; margin:0;">
                    <?= date('l, d F Y') ?>
                </p>
            </div>

            <!-- ── KPI STATS ── -->
            <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:14px; margin-bottom:24px;">

                <div class="kpi-card" style="border-top:3px solid #1d1d1f;">
                    <p class="kpi-num" style="color:#1d1d1f;"><?= $stats['total'] ?? 0 ?></p>
                    <p class="kpi-label">Total Laporan</p>
                </div>
                <div class="kpi-card" style="border-top:3px solid #f59e0b;">
                    <p class="kpi-num" style="color:#b45309;"><?= $stats['pending'] ?? 0 ?></p>
                    <p class="kpi-label">Pending</p>
                </div>
                <div class="kpi-card" style="border-top:3px solid #0066cc;">
                    <p class="kpi-num" style="color:#0066cc;"><?= $stats['in_progress'] ?? 0 ?></p>
                    <p class="kpi-label">Diproses</p>
                </div>
                <div class="kpi-card" style="border-top:3px solid #10b981;">
                    <p class="kpi-num" style="color:#065f46;"><?= $stats['resolved'] ?? 0 ?></p>
                    <p class="kpi-label">Selesai</p>
                </div>
                <div class="kpi-card" style="border-top:3px solid #ef4444;">
                    <p class="kpi-num" style="color:#991b1b;"><?= $stats['rejected'] ?? 0 ?></p>
                    <p class="kpi-label">Ditolak</p>
                </div>

            </div>

            <!-- ── MAP + TOP COMPLAINTS ── -->
            <div style="display:grid; grid-template-columns:1fr 360px; gap:16px; margin-bottom:16px;">

                <!-- Map card -->
                <div class="section-card">
                    <div style="padding:16px 20px; border-bottom:1px solid #e0e0e0; display:flex; align-items:center; justify-content:space-between;">
                        <h2 style="font-size:15px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin:0;">Peta Pengaduan</h2>
                        <a href="/admin/datatable" style="font-size:13px; color:#0066cc; text-decoration:none; letter-spacing:-0.12px;"
                           onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Kelola →</a>
                    </div>
                    <div id="adminMap" style="height:380px; width:100%;"></div>
                </div>

                <!-- Top complaints by upvote -->
                <div class="section-card" style="display:flex; flex-direction:column;">
                    <div style="padding:16px 20px; border-bottom:1px solid #e0e0e0; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
                        <h2 style="font-size:15px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin:0;">Top Upvote</h2>
                        <span style="font-size:11px; color:#7a7a7a; letter-spacing:-0.12px;">7 teratas</span>
                    </div>
                    <div id="topComplaintsList" style="padding:0 20px; flex:1; overflow-y:auto;"></div>
                </div>

            </div>

            <!-- ── CHARTS ROW ── -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">

                <div class="section-card" style="padding:20px 24px;">
                    <h3 style="font-size:15px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin:0 0 18px;">Laporan per Kategori</h3>
                    <canvas id="categoryChart" height="220"></canvas>
                </div>

                <div class="section-card" style="padding:20px 24px;">
                    <h3 style="font-size:15px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin:0 0 18px;">Distribusi Status</h3>
                    <canvas id="statusChart" height="220"></canvas>
                </div>

            </div>

            <!-- ── TREND LINE ── -->
            <div class="section-card" style="padding:20px 24px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
                    <h3 style="font-size:15px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin:0;">Tren Pengaduan Bulanan</h3>
                    <a href="/admin/analytics" style="font-size:13px; color:#0066cc; text-decoration:none; letter-spacing:-0.12px;"
                       onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Analitik Lengkap →</a>
                </div>
                <canvas id="trendChart" height="110"></canvas>
            </div>

        </div>
    </div><!-- /main -->

</div><!-- /shell -->

<!-- Scripts: Leaflet JS, MarkerCluster, Chart.js -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
/* ── Map ── */
var adminMap = L.map('adminMap').setView([-6.200000, 106.816666], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap', maxZoom: 19
}).addTo(adminMap);

var clusterGroup = L.markerClusterGroup({ chunkedLoading: true, maxClusterRadius: 45 });
adminMap.addLayer(clusterGroup);

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

function buildMarkers(complaints) {
    clusterGroup.clearLayers();
    complaints.forEach(function (c) {
        var color = statusColors[c.status] || '#6b7280';
        var m = L.marker([parseFloat(c.lat), parseFloat(c.lng)], {
            icon: L.divIcon({
                className: '',
                html: '<div style="background:' + color + ';width:24px;height:24px;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.25);"></div>',
                iconSize: [24, 24], iconAnchor: [12, 12]
            })
        });
        m.bindPopup(
            '<div style="font-family:system-ui,-apple-system,sans-serif;padding:12px 14px;min-width:200px;">' +
                '<p style="font-size:13px;font-weight:600;color:#1d1d1f;margin:0 0 4px;">' + (c.category_name || '-') + '</p>' +
                '<p style="font-size:11px;color:#7a7a7a;margin:0 0 8px;line-height:1.4;">' + (c.description || '').substring(0, 80) + '...</p>' +
                '<div style="display:flex;align-items:center;justify-content:space-between;">' +
                    '<span style="background:' + color + ';color:#fff;font-size:10px;font-weight:600;padding:2px 8px;border-radius:9999px;">' + (statusLabels[c.status] || c.status) + '</span>' +
                    '<a href="/complaint/' + c.id + '" style="color:#0066cc;font-size:12px;font-weight:600;text-decoration:none;">Detail →</a>' +
                '</div>' +
            '</div>'
        );
        clusterGroup.addLayer(m);
    });
}

function buildTopList(complaints) {
    var sorted = complaints.slice().sort(function (a, b) { return (b.upvotes || 0) - (a.upvotes || 0); }).slice(0, 7);
    var el = document.getElementById('topComplaintsList');
    if (!sorted.length) {
        el.innerHTML = '<p style="color:#7a7a7a;font-size:13px;padding:20px 0;">Belum ada data.</p>';
        return;
    }
    el.innerHTML = sorted.map(function (c) {
        var color = statusColors[c.status] || '#6b7280';
        return (
            '<div class="top-item" onclick="adminMap.setView([' + c.lat + ',' + c.lng + '],16,{animate:true})">' +
                '<div style="flex-shrink:0;width:9px;height:9px;border-radius:50%;background:' + color + ';margin-top:4px;"></div>' +
                '<div style="flex:1;min-width:0;">' +
                    '<p style="font-size:13px;font-weight:600;color:#1d1d1f;margin:0 0 2px;letter-spacing:-0.12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + (c.category_name || '-') + '</p>' +
                    '<p style="font-size:11px;color:#7a7a7a;margin:0;letter-spacing:-0.12px;">👍 ' + (c.upvotes || 0) + ' &bull; <span style="text-transform:capitalize;">' + (statusLabels[c.status] || c.status) + '</span></p>' +
                '</div>' +
                '<a href="/complaint/' + c.id + '" onclick="event.stopPropagation()" style="flex-shrink:0;font-size:11px;color:#0066cc;font-weight:600;text-decoration:none;" onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">→</a>' +
            '</div>'
        );
    }).join('');
}

/* Load complaints for map + top list */
var inlineComplaints = <?= json_encode($complaints ?? []) ?>;
if (inlineComplaints.length > 0) {
    buildMarkers(inlineComplaints);
    buildTopList(inlineComplaints);
    if (inlineComplaints.length > 0) {
        var lats = inlineComplaints.map(function(c){ return parseFloat(c.lat); });
        var lngs = inlineComplaints.map(function(c){ return parseFloat(c.lng); });
        adminMap.fitBounds([[Math.min.apply(null,lats), Math.min.apply(null,lngs)], [Math.max.apply(null,lats), Math.max.apply(null,lngs)]], {padding:[24,24]});
    }
} else {
    /* Fallback: fetch heatmap data for markers */
    fetch('/api/heatmap-data')
        .then(function(r){ return r.json(); })
        .then(function(res){
            var pts = res.data || [];
            pts.forEach(function(d){
                var m = L.circleMarker([parseFloat(d.lat), parseFloat(d.lng)], {
                    radius:7, color:'#0066cc', fillColor:'#0066cc', fillOpacity:0.7, weight:2
                });
                m.bindPopup('<div style="padding:8px;font-family:system-ui;font-size:12px;">👍 ' + (d.upvotes || 0) + ' upvote</div>');
                clusterGroup.addLayer(m);
            });
            document.getElementById('topComplaintsList').innerHTML = '<p style="color:#7a7a7a;font-size:13px;padding:20px 0;letter-spacing:-0.12px;">Tambahkan <code>$complaints</code> di controller admin untuk daftar lengkap.</p>';
        }).catch(function(){});
}

/* ── Charts ── */
Chart.defaults.font.family = "system-ui, -apple-system, 'Inter', sans-serif";
Chart.defaults.font.size   = 12;
Chart.defaults.color       = '#7a7a7a';

fetch('/api/chart-data')
    .then(function(r){ return r.json(); })
    .then(function(data){

        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: data.categories.map(function(d){ return d.category; }),
                datasets: [{
                    label: 'Laporan',
                    data: data.categories.map(function(d){ return d.total; }),
                    backgroundColor: '#0066cc',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero:true, ticks:{ stepSize:1 }, grid:{ color:'#f0f0f0' } },
                    x: { grid:{ display:false } }
                }
            }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: data.statuses.map(function(d){
                    return ({pending:'Pending', in_progress:'Diproses', resolved:'Selesai', rejected:'Ditolak'})[d.status] || d.status;
                }),
                datasets: [{
                    data: data.statuses.map(function(d){ return d.total; }),
                    backgroundColor: ['#f59e0b','#0066cc','#10b981','#ef4444'],
                    borderWidth: 3, borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: { legend: { position:'bottom', labels:{ padding:14, boxWidth:12 } } }
            }
        });

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: data.trend.map(function(d){ return d.label; }),
                datasets: [{
                    label: 'Laporan',
                    data: data.trend.map(function(d){ return d.total; }),
                    borderColor: '#0066cc',
                    backgroundColor: 'rgba(0,102,204,0.07)',
                    fill: true, tension: 0.35,
                    pointRadius: 3, pointBackgroundColor: '#0066cc'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero:true, ticks:{ stepSize:1 }, grid:{ color:'#f0f0f0' } },
                    x: { grid:{ display:false } }
                },
                plugins: { legend:{ display:false } }
            }
        });

    }).catch(function(){});
</script>
