<div class="max-w-7xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Manajemen Kategori</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-700">Daftar Kategori</h3>
            <button onclick="showCatModal()" class="bg-primary hover:bg-blue-800 text-white px-4 py-2 rounded text-sm transition">+ Tambah Kategori</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="p-3">ID</th>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Deskripsi</th>
                        <th class="p-3">Instansi</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="categoriesTbody"></tbody>
            </table>
        </div>
    </div>

    <!-- Category Modal -->
    <div id="catModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="font-semibold text-lg mb-4" id="catModalTitle">Tambah Kategori</h3>
            <form id="catForm" class="space-y-3">
                <input type="hidden" name="id" id="catId">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Kategori</label>
                    <input type="text" name="name" id="catName" required class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi</label>
                    <textarea name="description" id="catDescription" rows="2" class="w-full border rounded px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Instansi Penanggung Jawab</label>
                    <input type="text" name="agency" id="catAgency" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div class="flex gap-2 pt-3">
                    <button type="submit" class="flex-1 bg-primary text-white px-4 py-2 rounded text-sm hover:bg-blue-800 transition">Simpan</button>
                    <button type="button" onclick="closeCatModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
function loadCategories() {
    fetch('/api/admin/list-categories')
        .then(r => r.json())
        .then(res => {
            let html = '';
            if (res.data && res.data.length) {
                res.data.forEach(function(c) {
                    html += '<tr class="border-t">';
                    html += '<td class="p-3">' + c.id + '</td>';
                    html += '<td class="p-3 font-medium">' + c.name + '</td>';
                    html += '<td class="p-3 text-gray-500">' + (c.description || '-') + '</td>';
                    html += '<td class="p-3">' + (c.agency || '-') + '</td>';
                    html += '<td class="p-3"><div class="flex gap-1">';
                    html += '<button onclick="editCat(' + c.id + ',\'' + c.name.replace(/'/g, "\\'") + '\',\'' + (c.description || '').replace(/'/g, "\\'") + '\',\'' + (c.agency || '').replace(/'/g, "\\'") + '\')" class="text-blue-600 hover:underline text-xs">Edit</button>';
                    html += '<button onclick="deleteCat(' + c.id + ')" class="text-red-600 hover:underline text-xs">Hapus</button>';
                    html += '</div></td>';
                    html += '</tr>';
                });
            }
            document.getElementById('categoriesTbody').innerHTML = html || '<tr><td colspan="5" class="p-3 text-center text-gray-500">Tidak ada data</td></tr>';
        });
}

function showCatModal() {
    document.getElementById('catId').value = '';
    document.getElementById('catName').value = '';
    document.getElementById('catDescription').value = '';
    document.getElementById('catAgency').value = '';
    document.getElementById('catModalTitle').textContent = 'Tambah Kategori';
    document.getElementById('catModal').classList.remove('hidden');
}

function editCat(id, name, desc, agency) {
    document.getElementById('catId').value = id;
    document.getElementById('catName').value = name;
    document.getElementById('catDescription').value = desc;
    document.getElementById('catAgency').value = agency;
    document.getElementById('catModalTitle').textContent = 'Edit Kategori';
    document.getElementById('catModal').classList.remove('hidden');
}

function closeCatModal() {
    document.getElementById('catModal').classList.add('hidden');
}

document.getElementById('catForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('catId').value;
    const formData = new FormData(this);
    const data = new URLSearchParams(formData).toString();
    const url = id ? '/api/admin/update-category' : '/api/admin/create-category';
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: data + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) { closeCatModal(); loadCategories(); }
        else alert(res.message);
    });
});

function deleteCat(id) {
    if (!confirm('Yakin hapus kategori #' + id + '?')) return;
    fetch('/api/admin/delete-category', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'id=' + id + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) loadCategories();
        else alert(res.message);
    });
}

loadCategories();
</script>
