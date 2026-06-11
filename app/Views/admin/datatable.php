<div class="max-w-7xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Data Pengaduan</h1>
    <p class="text-gray-600 mb-6">Server-side table dengan pencarian dan filter.</p>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 border-b flex flex-wrap gap-3 items-center">
            <select id="dtStatusFilter" class="border rounded px-3 py-2 text-sm">
                <option value="all">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="in_progress">Diproses</option>
                <option value="resolved">Selesai</option>
                <option value="rejected">Ditolak</option>
            </select>
            <select id="dtCategoryFilter" class="border rounded px-3 py-2 text-sm">
                <option value="all">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button onclick="reloadTable()" class="bg-primary text-white px-4 py-2 rounded text-sm hover:bg-blue-800 transition">Terapkan Filter</button>
            <button class="text-sm text-gray-500 hover:text-gray-700 ml-auto" id="refreshBtn">&#8635; Refresh</button>
        </div>
        <div class="p-4 overflow-x-auto">
            <table id="complaintsTable" class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="p-3">ID</th>
                        <th class="p-3">Deskripsi</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Pelapor</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Upvote</th>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
const statusColors = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'in_progress': 'bg-blue-100 text-blue-800',
    'resolved': 'bg-green-100 text-green-800',
    'rejected': 'bg-red-100 text-red-800'
};
const statusLabels = {
    'pending': 'Pending',
    'in_progress': 'Diproses',
    'resolved': 'Selesai',
    'rejected': 'Ditolak'
};

const table = $('#complaintsTable').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: {
        url: '/api/admin/datatable-data',
        data: function(d) {
            d.status = $('#dtStatusFilter').val();
            d.category = $('#dtCategoryFilter').val();
        }
    },
    columns: [
        { data: 'id' },
        { data: 'description', render: d => d ? d.substring(0, 80) + (d.length > 80 ? '...' : '') : '-' },
        { data: 'category_name' },
        { data: 'reporter_name' },
        {
            data: 'status',
            render: function(d) {
                return '<span class="px-2 py-1 rounded-full text-xs font-semibold ' + (statusColors[d] || '') + '">' + (statusLabels[d] || d) + '</span>';
            }
        },
        { data: 'upvotes' },
        {
            data: 'created_at',
            render: function(d) {
                return new Date(d).toLocaleDateString('id-ID');
            }
        },
        {
            data: null,
            orderable: false,
            render: function(row) {
                let html = '<div class="flex gap-1 flex-wrap">';
                html += '<a href="/complaint/' + row.id + '" class="text-blue-600 hover:underline text-xs">Detail</a>';
                if (row.status !== 'resolved') {
                    html += '<select class="statusSelect text-xs border rounded px-1 py-0.5" data-id="' + row.id + '" onchange="changeStatus(this)">';
                    html += '<option value="">Update...</option>';
                    html += '<option value="pending"' + (row.status === 'pending' ? ' selected' : '') + '>Pending</option>';
                    html += '<option value="in_progress"' + (row.status === 'in_progress' ? ' selected' : '') + '>Diproses</option>';
                    html += '<option value="resolved"' + (row.status === 'resolved' ? ' selected' : '') + '>Selesai</option>';
                    html += '<option value="rejected"' + (row.status === 'rejected' ? ' selected' : '') + '>Ditolak</option>';
                    html += '</select>';
                }
                html += '</div>';
                return html;
            }
        }
    ],
    order: [[6, 'desc']],
    language: {
        search: 'Cari:',
        lengthMenu: 'Tampilkan _MENU_ data',
        info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
        paginate: { first: 'Awal', last: 'Akhir', next: '&rarr;', previous: '&larr;' },
        emptyTable: 'Tidak ada data pengaduan',
        processing: 'Memproses...',
        zeroRecords: 'Tidak ditemukan data yang cocok'
    }
});

function reloadTable() {
    table.ajax.reload();
}

document.getElementById('refreshBtn').addEventListener('click', function() { table.ajax.reload(); });

function changeStatus(sel) {
    const id = sel.dataset.id;
    const status = sel.value;
    if (!status) return;
    if (!confirm('Ubah status pengaduan #' + id + ' menjadi "' + status + '"?')) {
        sel.value = '';
        return;
    }
    fetch('/api/admin/update-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'complaint_id=' + id + '&status=' + status + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            table.ajax.reload();
        } else {
            alert(data.message);
            sel.value = '';
        }
    });
}
</script>
