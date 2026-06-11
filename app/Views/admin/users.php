<div class="max-w-7xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Manajemen Pengguna</h1>

    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-700">Daftar Pengguna</h3>
            <button onclick="showCreateModal()" class="bg-primary hover:bg-blue-800 text-white px-4 py-2 rounded text-sm transition">+ Tambah Pengguna</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="usersTable">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="p-3">ID</th>
                        <th class="p-3">Nama</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Telepon</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Tanggal Daftar</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTbody"></tbody>
            </table>
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div id="userModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="font-semibold text-lg mb-4" id="userModalTitle">Tambah Pengguna</h3>
            <form id="userForm" class="space-y-3">
                <input type="hidden" name="id" id="userId">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama</label>
                    <input type="text" name="name" id="userName" required class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" id="userEmail" required class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Telepon</label>
                    <input type="text" name="phone" id="userPhone" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Password <span class="text-gray-400 text-xs">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" id="userPassword" class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Role</label>
                    <select name="role" id="userRole" required class="w-full border rounded px-3 py-2 text-sm">
                        <option value="warga">Warga</option>
                        <option value="admin_instansi">Admin Instansi</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                </div>
                <div class="flex gap-2 pt-3">
                    <button type="submit" class="flex-1 bg-primary text-white px-4 py-2 rounded text-sm hover:bg-blue-800 transition">Simpan</button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
function loadUsers() {
    fetch('/api/admin/list-users')
        .then(r => r.json())
        .then(res => {
            let html = '';
            (res.data || []).forEach(function(u) {
                html += '<tr class="border-t">';
                html += '<td class="p-3">' + u.id + '</td>';
                html += '<td class="p-3 font-medium">' + u.name + '</td>';
                html += '<td class="p-3">' + u.email + '</td>';
                html += '<td class="p-3">' + (u.phone || '-') + '</td>';
                html += '<td class="p-3"><span class="px-2 py-0.5 rounded text-xs font-semibold ' + (u.role === 'super_admin' ? 'bg-red-100 text-red-800' : u.role === 'admin_instansi' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') + '">' + u.role + '</span></td>';
                html += '<td class="p-3">' + new Date(u.created_at).toLocaleDateString('id-ID') + '</td>';
                html += '<td class="p-3"><div class="flex gap-1">';
                html += '<button onclick="editUser(\'' + u.id + '\',\'' + u.name.replace(/'/g, "\\'") + '\',\'' + u.email + '\',\'' + (u.phone || '') + '\',\'' + u.role + '\')" class="text-blue-600 hover:underline text-xs">Edit</button>';
                html += '<button onclick="deleteUser(' + u.id + ')" class="text-red-600 hover:underline text-xs">Hapus</button>';
                html += '</div></td>';
                html += '</tr>';
            });
            document.getElementById('usersTbody').innerHTML = html || '<tr><td colspan="7" class="p-3 text-center text-gray-500">Tidak ada data</td></tr>';
        });
}

function showCreateModal() {
    document.getElementById('userId').value = '';
    document.getElementById('userName').value = '';
    document.getElementById('userEmail').value = '';
    document.getElementById('userPhone').value = '';
    document.getElementById('userPassword').value = '';
    document.getElementById('userRole').value = 'warga';
    document.getElementById('userModalTitle').textContent = 'Tambah Pengguna';
    document.getElementById('userModal').classList.remove('hidden');
    document.getElementById('userPassword').required = true;
}

function editUser(id, name, email, phone, role) {
    document.getElementById('userModalTitle').textContent = 'Edit Pengguna';
    document.getElementById('userId').value = id;
    document.getElementById('userName').value = name;
    document.getElementById('userEmail').value = email;
    document.getElementById('userPhone').value = phone;
    document.getElementById('userPassword').value = '';
    document.getElementById('userPassword').required = false;
    document.getElementById('userRole').value = role;
    document.getElementById('userModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}

document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('userId').value;
    const formData = new FormData(this);
    const data = new URLSearchParams(formData).toString();
    const url = id ? '/api/admin/update-user' : '/api/admin/create-user';
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: data + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeModal();
            loadUsers();
        } else {
            alert(res.message);
        }
    });
});

function deleteUser(id) {
    if (!confirm('Yakin hapus pengguna #' + id + '?')) return;
    fetch('/api/admin/delete-user', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'id=' + id + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) loadUsers();
        else alert(res.message);
    });
}

loadUsers();
</script>
