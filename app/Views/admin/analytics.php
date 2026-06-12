<?php $GLOBALS['ci_no_footer'] = true; ?>
<!-- ── ADMIN ANALYTICS ── -->
<div style="display:flex; height:calc(100vh - 44px); overflow:hidden;">

    <?= view('admin/sidebar', ['currentPage' => 'analytics']) ?>

    <div style="flex:1; min-width:0; overflow-y:auto; background:#f5f5f7;">
        <div style="max-width:1200px; margin:0 auto; padding:32px 32px 56px;">

            <!-- Page header -->
            <div style="margin-bottom:28px;">
                <p style="font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#0066cc; margin:0 0 4px;">Admin Panel</p>
                <h1 style="font-size:34px; font-weight:700; line-height:1.1; letter-spacing:-0.5px; color:#1d1d1f; margin:0;">Analitik Pengaduan</h1>
            </div>

            <!-- Charts row -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">

                <div style="background:#fff; border:1px solid #e0e0e0; border-radius:18px; padding:20px 24px;">
                    <h3 style="font-size:15px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin:0 0 18px;">Laporan per Kategori</h3>
                    <canvas id="categoryChart" height="250"></canvas>
                </div>

                <div style="background:#fff; border:1px solid #e0e0e0; border-radius:18px; padding:20px 24px;">
                    <h3 style="font-size:15px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin:0 0 18px;">Distribusi Status</h3>
                    <canvas id="statusChart" height="250"></canvas>
                </div>

            </div>

            <!-- Heatmap -->
            <div style="background:#fff; border:1px solid #e0e0e0; border-radius:18px; padding:20px 24px; margin-bottom:16px;">
                <h3 style="font-size:15px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin:0 0 18px;">Heatmap Konsentrasi Pengaduan</h3>
                <div id="heatmapContainer" style="width:100%; height:380px; border-radius:10px; overflow:hidden; border:1px solid #e0e0e0;"></div>
            </div>

            <!-- Trend chart -->
            <div style="background:#fff; border:1px solid #e0e0e0; border-radius:18px; padding:20px 24px;">
                <h3 style="font-size:15px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin:0 0 18px;">Tren Bulanan</h3>
                <canvas id="trendChart" height="160"></canvas>
            </div>

        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" crossorigin="anonymous"></script>

<script>
var heatmapMap = L.map('heatmapContainer').setView([-6.200000, 106.816666], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OSM', maxZoom:19 }).addTo(heatmapMap);

fetch('/api/heatmap-data')
    .then(function(r){ return r.json(); })
    .then(function(res) {
        var points = (res.data || []).map(function(d) {
            return [parseFloat(d.lat), parseFloat(d.lng), Math.max(0.3, (d.upvotes || 0) * 0.1 || 0.3)];
        });
        L.heatLayer(points, {
            radius:25, blur:15, maxZoom:17,
            gradient:{ 0.2:'#0066cc', 0.4:'#0ea5e9', 0.6:'#10b981', 0.8:'#f59e0b', 1.0:'#ef4444' }
        }).addTo(heatmapMap);
        if (points.length > 0) {
            var lats = points.map(function(p){ return p[0]; });
            var lngs = points.map(function(p){ return p[1]; });
            heatmapMap.fitBounds([
                [Math.min.apply(null,lats), Math.min.apply(null,lngs)],
                [Math.max.apply(null,lats), Math.max.apply(null,lngs)]
            ]);
        }
    });

Chart.defaults.font.family = "system-ui, -apple-system, 'Inter', sans-serif";
Chart.defaults.font.size   = 12;
Chart.defaults.color       = '#7a7a7a';

fetch('/api/chart-data')
    .then(function(r){ return r.json(); })
    .then(function(data) {

        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: data.categories.map(function(d){ return d.category; }),
                datasets: [{ label:'Laporan', data: data.categories.map(function(d){ return d.total; }), backgroundColor:'#0066cc', borderRadius:6 }]
            },
            options: {
                responsive:true,
                plugins:{ legend:{ display:false } },
                scales:{ y:{ beginAtZero:true, ticks:{ stepSize:1 }, grid:{ color:'#f0f0f0' } }, x:{ grid:{ display:false } } }
            }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: data.statuses.map(function(d){
                    return ({pending:'Pending', in_progress:'Diproses', resolved:'Selesai', rejected:'Ditolak'})[d.status] || d.status;
                }),
                datasets: [{ data: data.statuses.map(function(d){ return d.total; }), backgroundColor:['#f59e0b','#0066cc','#10b981','#ef4444'], borderWidth:3, borderColor:'#ffffff' }]
            },
            options: { responsive:true, cutout:'62%', plugins:{ legend:{ position:'bottom', labels:{ padding:14, boxWidth:12 } } } }
        });

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: data.trend.map(function(d){ return d.label; }),
                datasets: [{
                    label:'Laporan per Bulan',
                    data: data.trend.map(function(d){ return d.total; }),
                    borderColor:'#0066cc', backgroundColor:'rgba(0,102,204,0.07)',
                    fill:true, tension:0.35, pointRadius:3, pointBackgroundColor:'#0066cc'
                }]
            },
            options: {
                responsive:true,
                scales:{ y:{ beginAtZero:true, ticks:{ stepSize:1 }, grid:{ color:'#f0f0f0' } }, x:{ grid:{ display:false } } },
                plugins:{ legend:{ display:false } }
            }
        });

    }).catch(function(){});
</script>
