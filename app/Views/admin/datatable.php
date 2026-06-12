<?php $GLOBALS['ci_no_footer'] = true; ?>
<!-- ── DATA PENGADUAN ── -->
<style>
@media (max-width: 767px) {
    #adminShell   { display: block !important; height: auto !important; overflow: visible !important; }
    #adminMain    { height: auto !important; overflow: visible !important; }
    #datatablePad { padding: 20px 16px 80px !important; }
    .dt-filter-row { flex-direction: column !important; align-items: stretch !important; }
    .dt-filter-row select, .dt-filter-row button { width: 100% !important; min-width: unset !important; box-sizing: border-box; }
    #refreshBtn { margin-left: 0 !important; width: 100% !important; }
}
@media (min-width: 768px) and (max-width: 1023px) {
    #datatablePad { padding: 24px 20px 56px !important; }
}
</style>
<div id="adminShell" style="display:flex; height:calc(100vh - 44px); overflow:hidden;">

    <?= view('admin/sidebar', ['currentPage' => 'datatable']) ?>

    <div id="adminMain" style="flex:1; min-width:0; overflow-y:auto; background:#f5f5f7;">
    <div id="datatablePad" style="max-width:1280px; margin:0 auto; padding:32px 32px 56px;">

        <div style="margin-bottom:28px;">
            <p style="font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#0066cc; margin:0 0 4px;">Admin Panel</p>
            <h1 style="font-size:34px; font-weight:700; line-height:1.1; letter-spacing:-0.5px; color:#1d1d1f; margin:0;">Data Pengaduan</h1>
        </div>

        <div class="card-utility" style="padding:0; overflow:hidden;">

            <!-- Filter strip -->
            <div class="dt-filter-row" style="padding:16px 24px; border-bottom:1px solid #e0e0e0; display:flex; flex-wrap:wrap; gap:8px; align-items:center; background:#ffffff;">
                <select id="dtStatusFilter" class="input-field" style="width:auto; padding:7px 12px; font-size:14px; min-width:130px;">
                    <option value="all">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">Diproses</option>
                    <option value="resolved">Selesai</option>
                    <option value="rejected">Ditolak</option>
                </select>
                <select id="dtCategoryFilter" class="input-field" style="width:auto; padding:7px 12px; font-size:14px; min-width:150px;">
                    <option value="all">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button onclick="reloadTable()" class="btn-pill" style="font-size:14px; letter-spacing:-0.224px; padding:7px 16px;">
                    Terapkan Filter
                </button>
                <button id="refreshBtn" class="btn-utility" style="margin-left:auto;">
                    &#8635; Refresh
                </button>
            </div>

            <!-- Table -->
            <div style="padding:16px 24px; overflow-x:auto; background:#ffffff;">
                <table id="complaintsTable" style="width:100%; font-size:14px; letter-spacing:-0.224px; color:#1d1d1f; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f5f5f7; text-align:left;">
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">ID</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Deskripsi</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Kategori</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Pelapor</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Status</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Upvote</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Tanggal</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div><!-- /max-width -->
    </div><!-- /main scroll -->

</div><!-- /shell -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<style>
/* Override DataTables chrome to match design system */
.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 14px;
    font-family: inherit;
    color: #1d1d1f;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 8px !important;
    font-size: 13px;
    color: #1d1d1f !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #0066cc !important;
    color: #ffffff !important;
    border-color: #0066cc !important;
}
.dataTables_wrapper .dataTables_info { font-size: 13px; color: #7a7a7a; }
table.dataTable tbody tr { border-bottom: 1px solid #f0f0f0; }
table.dataTable tbody tr:hover { background: #f5f5f7; }
</style>

<script>
/* ─────────────────────────────────────────────────────────────
   CSRF helper — always reads the LATEST hash from CI4's cookie
   so token-regeneration (Security::$regenerate = true) doesn't
   invalidate subsequent requests.
   ───────────────────────────────────────────────────────────── */
var _csrfName   = '<?= csrf_token() ?>';                                   /* field name  */
var _csrfCookie = '<?= config("Security")->cookieName ?? "csrf_cookie_name" ?>'; /* cookie name */
var _csrfFallback = '<?= csrf_hash() ?>';                                  /* initial hash */

function getCsrf() {
    try {
        var re    = new RegExp('(?:^|; )' + _csrfCookie.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)');
        var match = document.cookie.match(re);
        return match ? decodeURIComponent(match[1]) : _csrfFallback;
    } catch (e) {
        return _csrfFallback;
    }
}

/* ─────────────────────────────────────────────────────────────
   Suppress DataTables built-in alert() on AJAX errors —
   prevents the alert from blocking all subsequent UI events.
   ───────────────────────────────────────────────────────────── */
$.fn.dataTable.ext.errMode = 'none';

var statusColors = {
    'pending':     'status-pending',
    'in_progress': 'status-in_progress',
    'resolved':    'status-resolved',
    'rejected':    'status-rejected'
};
var statusLabels = {
    'pending':     'Pending',
    'in_progress': 'Diproses',
    'resolved':    'Selesai',
    'rejected':    'Ditolak'
};

/* ─── DataTables init ─── */
var table = $('#complaintsTable').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: {
        url: '/api/admin/datatable-data',
        type: 'GET',
        data: function (d) {
            d.status   = $('#dtStatusFilter').val();
            d.category = $('#dtCategoryFilter').val();
        },
        /* Force-clear processing indicator on AJAX failure so UI stays interactive */
        error: function (xhr, error, thrown) {
            console.warn('DataTables AJAX error:', error, xhr.status, thrown);
            /* DT 1.13.x: hide processing div directly */
            $('#complaintsTable').closest('.dataTables_wrapper').find('.dataTables_processing').hide();
        }
    },
    columns: [
        { data: 'id', width: '50px' },
        {
            data: 'description',
            render: function (d) { return d ? (d.length > 80 ? d.substring(0, 80) + '…' : d) : '-'; }
        },
        { data: 'category_name' },
        { data: 'reporter_name' },
        {
            data: 'status',
            render: function (d) {
                return '<span class="status-pill ' + (statusColors[d] || '') + '">' + (statusLabels[d] || d) + '</span>';
            }
        },
        { data: 'upvotes', width: '70px' },
        {
            data: 'created_at',
            render: function (d) { return d ? new Date(d).toLocaleDateString('id-ID') : '-'; }
        },
        {
            data: null,
            orderable: false,
            render: function (_, __, row) {
                var html = '<div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">';
                html += '<a href="/complaint/' + row.id + '" style="color:#0066cc;font-size:13px;text-decoration:none;"'
                      + ' onmouseover="this.style.textDecoration=\'underline\'"'
                      + ' onmouseout="this.style.textDecoration=\'none\'">Detail</a>';
                if (row.status !== 'resolved') {
                    html += '<select style="font-size:13px;border:1px solid #e0e0e0;border-radius:8px;padding:3px 8px;color:#1d1d1f;font-family:inherit;"'
                          + ' data-id="' + row.id + '" onchange="changeStatus(this)">';
                    html += '<option value="">Update…</option>';
                    ['pending', 'in_progress', 'resolved', 'rejected'].forEach(function (s) {
                        html += '<option value="' + s + '"' + (row.status === s ? ' selected' : '') + '>' + (statusLabels[s] || s) + '</option>';
                    });
                    html += '</select>';
                }
                html += '</div>';
                return html;
            }
        }
    ],
    order: [[6, 'desc']],
    language: {
        search:        'Cari:',
        lengthMenu:    'Tampilkan _MENU_ data',
        info:          'Menampilkan _START_–_END_ dari _TOTAL_ data',
        paginate:      { first: 'Awal', last: 'Akhir', next: '&rarr;', previous: '&larr;' },
        emptyTable:    'Tidak ada data pengaduan',
        processing:    'Memuat…',
        zeroRecords:   'Data tidak ditemukan'
    }
});

/* Log DataTables error events without blocking UI */
table.on('error.dt', function (e, settings, techNote, message) {
    console.error('DataTables error:', message);
});

/* ─── Filter & Refresh ─── */
function reloadTable() { table.ajax.reload(null, false); }
document.getElementById('refreshBtn').addEventListener('click', function () { table.ajax.reload(null, false); });

/* ─── Status change ───────────────────────────────────────────
   Uses getCsrf() to always send the LATEST token, avoiding
   CI4's CSRF regeneration from invalidating subsequent POSTs.
   ─────────────────────────────────────────────────────────── */
function changeStatus(sel) {
    var id     = sel.getAttribute('data-id');
    var status = sel.value;
    if (!status) return;

    var label = statusLabels[status] || status;
    if (!confirm('Ubah status pengaduan #' + id + ' menjadi "' + label + '"?')) {
        sel.value = '';
        return;
    }

    /* Disable select during request to prevent double-submit */
    sel.disabled = true;

    fetch('/api/admin/update-status', {
        method:  'POST',
        headers: {
            'Content-Type':     'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'complaint_id=' + encodeURIComponent(id)
            + '&status='      + encodeURIComponent(status)
            + '&'             + encodeURIComponent(_csrfName)
            + '='             + encodeURIComponent(getCsrf())
    })
    .then(function (r) {
        /* If CI4 returned a non-2xx (e.g. 403 CSRF), throw a descriptive error */
        if (!r.ok) {
            throw new Error('SERVER_' + r.status);
        }
        return r.json();
    })
    .then(function (data) {
        if (data.success) {
            /* Keep current page position after reload */
            table.ajax.reload(null, false);
        } else {
            alert(data.message || 'Gagal memperbarui status.');
            sel.value    = '';
            sel.disabled = false;
        }
    })
    .catch(function (err) {
        console.error('changeStatus error:', err);
        sel.disabled = false;
        sel.value    = '';

        if (err.message && err.message.indexOf('SERVER_403') !== -1) {
            /* CSRF expired — reload page to get fresh token */
            if (confirm('Sesi keamanan habis. Muat ulang halaman sekarang?')) {
                location.reload();
            }
        } else {
            alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
        }
    });
}
</script>
