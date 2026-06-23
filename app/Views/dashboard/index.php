<?php $GLOBALS['ci_no_footer'] = true; ?>
<style>
.complaint-item { border-bottom:1px solid #f0f0f0; transition:background 0.12s; cursor:pointer; }
.complaint-item:hover { background:#f5f5f7; }
.complaint-item:last-child { border-bottom:none; }
.complaint-item.active { background:#f0f5ff; border-left:3px solid #0066cc; }
#listPane::-webkit-scrollbar { width:4px; }
#listPane::-webkit-scrollbar-track { background:#f5f5f7; }
#listPane::-webkit-scrollbar-thumb { background:#cccccc; border-radius:2px; }
.leaflet-popup-content-wrapper { border-radius:12px !important; padding:0 !important; }
.leaflet-popup-content { margin:0 !important; }
#mapPane { position: relative; z-index: 0; }

/* complaint list search, sort, pagination */
.comp-search-wrapper {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 16px; border-bottom:1px solid #e0e0e0;
  position:sticky; top:0; background:#fff; z-index:5;
}
.comp-search-wrapper .search-icon {
  flex-shrink:0; color:#7a7a7a; font-size:15px;
}
.comp-search-wrapper input {
  flex:1; border:none; outline:none; font-size:14px;
  font-family:inherit; color:#1d1d1f; background:transparent;
  letter-spacing:-0.224px;
}
.comp-search-wrapper input::placeholder { color:#aaaaaa; }
.comp-search-wrapper .clear-btn {
  flex-shrink:0; background:none; border:none; cursor:pointer;
  color:#7a7a7a; font-size:16px; padding:2px; display:none;
  line-height:1;
}
.comp-search-wrapper .clear-btn.visible { display:block; }

.comp-filter-row {
  display:flex; gap:6px; padding:6px 16px;
  border-bottom:1px solid #f0f0f0;
  position:sticky; top:47px; background:#fff; z-index:5;
}
.comp-filter-row select {
  flex:1; font-size:12px; padding:4px 8px;
  border:1px solid #e0e0e0; border-radius:9999px;
  font-family:inherit; color:#1d1d1f; background:#fff;
  outline:none; cursor:pointer; min-width:0;
  letter-spacing:-0.12px;
  -webkit-appearance:none; appearance:none;
  background-image:url("data:image/svg+xml,%3Csvg width='8' height='5' viewBox='0 0 8 5' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L4 4L7 1' stroke='%237a7a7a' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
  background-repeat:no-repeat; background-position:right 8px center; padding-right:24px;
}
.comp-filter-row select:focus { border-color:#0071e3; }

.pagination-bar {
  display:flex; align-items:center; justify-content:center; gap:4px;
  padding:12px 16px; border-top:1px solid #e0e0e0;
  position:sticky; bottom:0; background:#fff; z-index:5;
}
.pagination-bar .page-btn {
  min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;
  border:1px solid #e0e0e0; border-radius:8px; background:#fff;
  font-size:12px; color:#1d1d1f; cursor:pointer; transition:all 0.1s;
  font-family:inherit; padding:0 6px;
}
.pagination-bar .page-btn:hover { background:#f5f5f7; }
.pagination-bar .page-btn.active { background:#0066cc; color:#fff; border-color:#0066cc; }
.pagination-bar .page-btn:disabled { opacity:0.35; cursor:default; }
.pagination-bar .page-info { font-size:11px; color:#7a7a7a; white-space:nowrap; margin:0 4px; letter-spacing:-0.12px; }
.pagination-bar .page-size-select {
  font-size:11px; padding:2px 6px; border:1px solid #e0e0e0;
  border-radius:6px; font-family:inherit; color:#7a7a7a; background:#fff;
  outline:none; cursor:pointer; margin-left:auto;
  letter-spacing:-0.12px;
}

.sort-trigger {
  font-size:12px; color:#7a7a7a; cursor:pointer; display:flex; align-items:center; gap:4px;
  padding:4px 10px; border:1px solid #e0e0e0; border-radius:9999px;
  background:#fff; letter-spacing:-0.12px; white-space:nowrap;
  transition:border-color 0.1s;
  -webkit-appearance:none; appearance:none;
  font-family:inherit;
}
.sort-trigger:hover { border-color:#0071e3; }
.sort-trigger:focus { outline:none; border-color:#0071e3; }

.comp-list-scroll { flex:1; overflow-y:auto; }

@media (max-width: 767px) {
    .sub-nav-frosted  { height: auto !important; overflow: visible !important; }
    .dash-filter-inner {
        flex-wrap: wrap !important;
        height: auto !important;
        padding: 10px 14px !important;
        gap: 8px !important;
        align-items: flex-start !important;
        justify-content: flex-start !important;
    }
    .dash-filter-inner > span { flex: 0 0 100%; }
    .dash-filter-inner > div  { display: flex !important; flex-wrap: wrap !important; gap: 8px !important; width: 100%; }
    .dash-filter-inner select { flex: 1 1 130px !important; min-width: unset !important; }
    .dash-filter-inner a.btn-pill { flex: 1 1 auto !important; text-align: center; white-space: nowrap; }
    #dashLayout { flex-direction: column !important; height: auto !important; overflow: visible !important; }
    #mapPane    { height: 40vh !important; flex: none !important; min-width: 0 !important; }
    #listPane   { width: 100% !important; flex: none !important; height: 55vh !important; }
    .dash-divider { display: none !important; }
    .comp-filter-row select { font-size:11px; padding:3px 16px 3px 8px; }
    .comp-search-wrapper { padding:6px 12px; }
    .pagination-bar .page-btn { min-width:28px; height:28px; font-size:11px; }
}
</style>

<!-- ── FILTER STRIP ── -->
<div class="sub-nav-frosted">
    <div class="dash-filter-inner" style="max-width:100%; padding:0 20px; display:flex; align-items:center; justify-content:space-between; height:52px; gap:12px;">
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

<!-- ── SPLIT LAYOUT ── -->
<div id="dashLayout" style="display:flex; height:calc(100vh - 96px); overflow:hidden;">

    <!-- Map pane -->
    <div id="mapPane" style="flex:1; min-width:0; position:relative;">
        <div id="map" style="height:100%; width:100%;"></div>
    </div>

    <div class="dash-divider" style="width:1px; background:#e0e0e0; flex-shrink:0;"></div>

    <!-- Complaint list pane -->
    <div id="listPane" style="width:380px; flex-shrink:0; overflow-y:auto; background:#ffffff; display:flex; flex-direction:column;">

        <!-- Sticky header: title + sort -->
        <div style="padding:14px 16px 0; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; background:#fff; z-index:5;">
            <div>
                <span style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f;">Pengaduan</span>
                <span id="complaint-count" style="font-size:12px; letter-spacing:-0.12px; color:#7a7a7a; margin-left:6px;"></span>
            </div>
            <select id="sortSelect" class="sort-trigger" aria-label="Urutkan">
                <option value="newest">Terbaru</option>
                <option value="oldest">Terlama</option>
                <option value="upvotes_desc">Upvotes &darr;</option>
                <option value="upvotes_asc">Upvotes &uarr;</option>
                <option value="status">Status</option>
                <option value="category">Kategori</option>
            </select>
        </div>

        <!-- Search bar -->
        <div class="comp-search-wrapper">
            <span class="search-icon">&#128269;</span>
            <input type="text" id="searchInput" placeholder="Cari pengaduan..." autocomplete="off">
            <button class="clear-btn" id="clearSearchBtn" aria-label="Hapus pencarian">&times;</button>
        </div>

        <!-- Category + Status filter pills -->
        <div class="comp-filter-row">
            <select id="listFilterCategory">
                <option value="all">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="listFilterStatus">
                <option value="all">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="in_progress">Diproses</option>
                <option value="resolved">Selesai</option>
                <option value="rejected">Ditolak</option>
            </select>
        </div>

        <!-- Scrollable list -->
        <div id="complaint-list" class="comp-list-scroll"></div>

        <!-- Pagination bar -->
        <div class="pagination-bar" id="paginationBar">
            <button class="page-btn" id="pagePrev" disabled>&lsaquo;</button>
            <div id="pageNumbers" style="display:flex; align-items:center; gap:2px;"></div>
            <button class="page-btn" id="pageNext" disabled>&rsaquo;</button>
            <select class="page-size-select" id="pageSizeSelect">
                <option value="10">10/hal</option>
                <option value="20" selected>20/hal</option>
                <option value="50">50/hal</option>
                <option value="100">100/hal</option>
            </select>
        </div>
    </div>
</div>

<!-- Leaflet + MarkerCluster -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js" crossorigin="anonymous"></script>

<script>
var map = L.map('map').setView([-6.200000, 106.816666], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

var markerGroup = L.markerClusterGroup({ chunkedLoading: true, maxClusterRadius: 50 });
var allComplaints = <?= json_encode($complaints ?? []) ?>;
var markerMap = {};

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

/* ── Search / Sort / Pagination State ── */
var state = {
    search: '',
    sortBy: 'newest',
    filterCategory: 'all',
    filterStatus: 'all',
    page: 1,
    perPage: 20
};

/* ── Derive filtered + sorted data ── */
function getProcessedData() {
    var data = allComplaints;

    // search
    if (state.search) {
        var q = state.search.toLowerCase();
        data = data.filter(function(c) {
            return (c.description && c.description.toLowerCase().indexOf(q) !== -1) ||
                   (c.category_name && c.category_name.toLowerCase().indexOf(q) !== -1) ||
                   (c.reporter_name && c.reporter_name.toLowerCase().indexOf(q) !== -1);
        });
    }

    // category filter
    if (state.filterCategory !== 'all') {
        data = data.filter(function(c) { return c.category_id == state.filterCategory; });
    }

    // status filter
    if (state.filterStatus !== 'all') {
        data = data.filter(function(c) { return c.status === state.filterStatus; });
    }

    // sort
    var sort = state.sortBy;
    data.sort(function(a, b) {
        switch (sort) {
            case 'newest':
                return (a.created_at > b.created_at) ? -1 : (a.created_at < b.created_at) ? 1 : 0;
            case 'oldest':
                return (a.created_at < b.created_at) ? -1 : (a.created_at > b.created_at) ? 1 : 0;
            case 'upvotes_desc':
                return (parseInt(b.upvotes) || 0) - (parseInt(a.upvotes) || 0);
            case 'upvotes_asc':
                return (parseInt(a.upvotes) || 0) - (parseInt(b.upvotes) || 0);
            case 'status':
                return (a.status > b.status) ? 1 : (a.status < b.status) ? -1 : 0;
            case 'category':
                return (a.category_name > b.category_name) ? 1 : (a.category_name < b.category_name) ? -1 : 0;
            default:
                return 0;
        }
    });

    return data;
}

/* ── Render map markers from filtered data (full set, not just page) ── */
function renderMarkers(filtered) {
    markerGroup.clearLayers();
    markerMap = {};

    filtered.forEach(function(c) {
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
}

/* ── Render paginated list ── */
function renderList(filtered) {
    var countEl = document.getElementById('complaint-count');
    var listEl  = document.getElementById('complaint-list');
    var total   = filtered.length;

    countEl.textContent = total + ' laporan';

    if (total === 0) {
        listEl.innerHTML = '<div style="padding:40px 20px;text-align:center;color:#7a7a7a;font-size:14px;letter-spacing:-0.224px;">Tidak ada pengaduan ditemukan.</div>';
        return;
    }

    var start = (state.page - 1) * state.perPage;
    var end   = Math.min(start + state.perPage, total);
    var pageItems = filtered.slice(start, end);

    listEl.innerHTML = pageItems.map(function(c) {
        var color = statusColors[c.status] || '#6b7280';
        var label = statusLabels[c.status] || c.status;
        var desc  = (c.description || '').substring(0, 80) + ((c.description || '').length > 80 ? '...' : '');
        return (
            '<div class="complaint-item" id="item-' + c.id + '"' +
                ' onclick="focusComplaint(' + c.id + ',' + c.lat + ',' + c.lng + ')"' +
                ' style="padding:12px 16px;display:flex;align-items:flex-start;gap:10px;">' +
                '<div style="flex-shrink:0;width:10px;height:10px;border-radius:50%;background:' + color + ';margin-top:4px;"></div>' +
                '<div style="flex:1;min-width:0;">' +
                    '<div style="display:flex;align-items:center;gap:6px;margin-bottom:2px;flex-wrap:wrap;">' +
                        '<span style="font-size:10px;font-weight:700;letter-spacing:0.4px;color:' + color + ';text-transform:uppercase;">' + label + '</span>' +
                        '<span style="font-size:11px;color:#7a7a7a;">&#128077; ' + (c.upvotes || 0) + '</span>' +
                        '<span style="font-size:11px;color:#cccccc;">' + formatDate(c.created_at) + '</span>' +
                    '</div>' +
                    '<p style="font-size:14px;font-weight:600;color:#1d1d1f;margin:0 0 2px;letter-spacing:-0.224px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + (c.category_name || '-') + '</p>' +
                    '<p style="font-size:12px;color:#7a7a7a;margin:0;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">' + desc + '</p>' +
                '</div>' +
                '<a href="/complaint/' + c.id + '"' +
                   ' onclick="event.stopPropagation()"' +
                   ' style="flex-shrink:0;color:#0066cc;font-size:11px;font-weight:600;text-decoration:none;letter-spacing:-0.12px;margin-top:2px;white-space:nowrap;"' +
                   ' onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">Detail &rarr;</a>' +
            '</div>'
        );
    }).join('');
}

/* ── Render pagination controls ── */
function renderPagination(total) {
    var totalPages = Math.max(1, Math.ceil(total / state.perPage));
    var page = state.page;

    var prevBtn = document.getElementById('pagePrev');
    var nextBtn = document.getElementById('pageNext');
    var numContainer = document.getElementById('pageNumbers');

    prevBtn.disabled = (page <= 1);
    nextBtn.disabled = (page >= totalPages);

    var pages = [];
    var range = 2;

    // always show first page
    pages.push(1);

    var startPage = Math.max(2, page - range);
    var endPage = Math.min(totalPages - 1, page + range);

    if (startPage > 2) pages.push('ellipsis');

    for (var i = startPage; i <= endPage; i++) {
        pages.push(i);
    }

    if (endPage < totalPages - 1) pages.push('ellipsis');

    if (totalPages > 1) pages.push(totalPages);

    numContainer.innerHTML = pages.map(function(p) {
        if (p === 'ellipsis') {
            return '<span style="font-size:11px;color:#cccccc;padding:0 2px;">&hellip;</span>';
        }
        return '<button class="page-btn' + (p === page ? ' active' : '') + '" data-page="' + p + '">' + p + '</button>';
    }).join('');

    // attach page click events
    numContainer.querySelectorAll('.page-btn[data-page]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            goToPage(parseInt(this.dataset.page));
        });
    });
}

/* ── Navigate to page ── */
function goToPage(p) {
    var filtered = getProcessedData();
    var totalPages = Math.max(1, Math.ceil(filtered.length / state.perPage));
    if (p < 1) p = 1;
    if (p > totalPages) p = totalPages;
    if (p === state.page) return;
    state.page = p;

    renderMarkers(filtered);
    renderList(filtered);
    renderPagination(filtered.length);

    document.getElementById('complaint-list').scrollTop = 0;

    // update URL hash
    window.location.hash = 'page=' + p;
}

/* ── Full render: markers + list + pagination ── */
function renderAll() {
    var filtered = getProcessedData();
    var totalPages = Math.max(1, Math.ceil(filtered.length / state.perPage));

    // clamp page
    if (state.page > totalPages) state.page = totalPages;

    renderMarkers(filtered);
    renderList(filtered);
    renderPagination(filtered.length);
}

/* ── Focus complaint on map ── */
function focusComplaint(id, lat, lng) {
    document.querySelectorAll('.complaint-item.active').forEach(function(el) {
        el.classList.remove('active');
    });
    var itemEl = document.getElementById('item-' + id);
    if (itemEl) itemEl.classList.add('active');

    var m = markerMap[id];
    if (!m) {
        map.setView([parseFloat(lat), parseFloat(lng)], 16, { animate: true });
        return;
    }
    markerGroup.zoomToShowLayer(m, function() { m.openPopup(); });
}

/* ── Helper: format date ── */
function formatDate(dateStr) {
    if (!dateStr) return '';
    var d = new Date(dateStr);
    var months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
}

/* ── Debounce utility ── */
function debounce(fn, ms) {
    var timer;
    return function() {
        var ctx = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function() { fn.apply(ctx, args); }, ms);
    };
}

/* ── Search input handler ── */
document.getElementById('searchInput').addEventListener('input', debounce(function() {
    state.search = this.value.trim();
    state.page = 1;
    renderAll();

    var clearBtn = document.getElementById('clearSearchBtn');
    clearBtn.classList.toggle('visible', this.value.length > 0);
}, 200));

document.getElementById('clearSearchBtn').addEventListener('click', function() {
    var input = document.getElementById('searchInput');
    input.value = '';
    state.search = '';
    state.page = 1;
    renderAll();
    this.classList.remove('visible');
    input.focus();
});

/* ── Sort handler ── */
document.getElementById('sortSelect').addEventListener('change', function() {
    state.sortBy = this.value;
    state.page = 1;
    renderAll();
});

/* ── List filter handlers ── */
document.getElementById('listFilterCategory').addEventListener('change', function() {
    state.filterCategory = this.value;
    state.page = 1;
    renderAll();
    // sync top filter strip
    document.getElementById('filterCategory').value = this.value;
});

document.getElementById('listFilterStatus').addEventListener('change', function() {
    state.filterStatus = this.value;
    state.page = 1;
    renderAll();
    document.getElementById('filterStatus').value = this.value;
});

/* ── Page size handler ── */
document.getElementById('pageSizeSelect').addEventListener('change', function() {
    state.perPage = parseInt(this.value);
    state.page = 1;
    renderAll();
});

/* ── Prev / Next buttons ── */
document.getElementById('pagePrev').addEventListener('click', function() {
    if (!this.disabled) goToPage(state.page - 1);
});
document.getElementById('pageNext').addEventListener('click', function() {
    if (!this.disabled) goToPage(state.page + 1);
});

/* ── Top filter strip syncs with list filters ── */
document.getElementById('filterCategory').addEventListener('change', function() {
    state.filterCategory = this.value;
    state.page = 1;
    document.getElementById('listFilterCategory').value = this.value;
    renderAll();
});
document.getElementById('filterStatus').addEventListener('change', function() {
    state.filterStatus = this.value;
    state.page = 1;
    document.getElementById('listFilterStatus').value = this.value;
    renderAll();
});

/* ── Keyboard shortcut: focus search with Ctrl+F or / ── */
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
    if (e.key === '/' && !e.ctrlKey && !e.metaKey && !e.target.matches('input, textarea, select')) {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
});

/* ── Initial render ── */
renderAll();

/* ── Restore page from hash ── */
if (window.location.hash) {
    var match = window.location.hash.match(/page=(\d+)/);
    if (match) {
        state.page = parseInt(match[1]);
        renderAll();
    }
}

/* ── User location ── */
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
        var lat = pos.coords.latitude, lng = pos.coords.longitude;
        map.setView([lat, lng], 14);
        L.circleMarker([lat, lng], {
            radius: 8, color: '#0066cc', fillColor: '#0066cc', fillOpacity: 0.8
        }).addTo(map).bindPopup('<b style="font-size:13px;">Lokasi Anda</b>').openPopup();
    });
}
</script>
