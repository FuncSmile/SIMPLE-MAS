<div class="max-w-7xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Analitik Pengaduan</h1>

    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="font-semibold text-gray-700 mb-4">&#128202; Tren Kategori (Bar Chart)</h3>
            <canvas id="categoryChart" height="250"></canvas>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="font-semibold text-gray-700 mb-4">&#127761; Status Penyelesaian (Pie Chart)</h3>
            <canvas id="statusChart" height="250"></canvas>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h3 class="font-semibold text-gray-700 mb-4">&#128293; Heatmap Konsentrasi Pengaduan</h3>
        <div id="heatmapContainer" class="w-full h-96 rounded-lg border"></div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="font-semibold text-gray-700 mb-4">&#128200; Tren Bulanan</h3>
        <canvas id="trendChart" height="200"></canvas>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Heatmap
const heatmapMap = L.map('heatmapContainer').setView([-6.200000, 106.816666], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OSM',
    maxZoom: 19
}).addTo(heatmapMap);

fetch('/api/heatmap-data')
    .then(r => r.json())
    .then(res => {
        const points = res.data.map(d => [parseFloat(d.lat), parseFloat(d.lng), Math.max(0.3, d.upvotes * 0.1 || 0.3)]);
        L.heatLayer(points, {
            radius: 25,
            blur: 15,
            maxZoom: 17,
            gradient: { 0.2: 'blue', 0.4: 'cyan', 0.6: 'lime', 0.8: 'yellow', 1.0: 'red' }
        }).addTo(heatmapMap);

        if (points.length > 0) {
            const lats = points.map(p => p[0]), lngs = points.map(p => p[1]);
            heatmapMap.fitBounds([[Math.min(...lats), Math.min(...lngs)], [Math.max(...lats), Math.max(...lngs)]]);
        }
    });

// Charts
fetch('/api/chart-data')
    .then(r => r.json())
    .then(data => {
        // Bar Chart - Categories
        const catCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: data.categories.map(d => d.category),
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: data.categories.map(d => d.total),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Pie Chart - Status
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: data.statuses.map(d => {
                    const labels = { 'pending': 'Pending', 'in_progress': 'Diproses', 'resolved': 'Selesai', 'rejected': 'Ditolak' };
                    return labels[d.status] || d.status;
                }),
                datasets: [{
                    data: data.statuses.map(d => d.total),
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Line Chart - Trend
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: data.trend.map(d => d.label),
                datasets: [{
                    label: 'Laporan per Bulan',
                    data: data.trend.map(d => d.total),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                plugins: { legend: { display: false } }
            }
        });
    });
</script>
