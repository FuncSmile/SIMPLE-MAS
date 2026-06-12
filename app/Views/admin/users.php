<?php $GLOBALS['ci_no_footer'] = true; ?>
<!-- ── MANAJEMEN PENGGUNA ── -->
<div style="display:flex; height:calc(100vh - 44px); overflow:hidden;">

    <?= view('admin/sidebar', ['currentPage' => 'users']) ?>

    <div style="flex:1; min-width:0; overflow-y:auto; background:#f5f5f7;">
    <div style="max-width:1100px; margin:0 auto; padding:32px 32px 56px;">

        <div style="margin-bottom:28px;">
            <p style="font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#0066cc; margin:0 0 4px;">Admin Panel</p>
            <h1 style="font-size:34px; font-weight:700; line-height:1.1; letter-spacing:-0.5px; color:#1d1d1f; margin:0;">Manajemen Pengguna</h1>
        </div>

        <div class="card-utility" style="padding:0; overflow:hidden;">
            <!-- Card header -->
            <div style="padding:16px 24px; border-bottom:1px solid #e0e0e0; display:flex; align-items:center; justify-content:space-between; background:#ffffff;">
                <p style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f; margin:0;">Daftar Pengguna</p>
                <button onclick="showCreateModal()" class="btn-pill" style="font-size:14px; letter-spacing:-0.224px; padding:8px 16px;">
                    + Tambah Pengguna
                </button>
            </div>
            <!-- Table -->
            <div style="padding:0 24px 16px; overflow-x:auto; background:#ffffff;">
                <table style="width:100%; font-size:14px; letter-spacing:-0.224px; color:#1d1d1f; border-collapse:collapse;" id="usersTable">
                    <thead>
                        <tr style="background:#f5f5f7; text-align:left;">
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">ID</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Nama</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Email</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Telepon</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Role</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Tanggal Daftar</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="usersTbody"></tbody>
                </table>
            </div>
        </div>
    </div><!-- /max-width -->
    </div><!-- /main scroll -->

</div><!-- /shell -->

<!-- Modal: Create / Edit User -->
<div id="userModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:500; align-items:center; justify-content:center; padding:20px;">
    <div class="card-utility" style="width:100%; max-width:440px; position:relative;">
        <h3 id="userModalTitle" style="font-size:21px; font-weight:600; letter-spacing:0.231px; color:#1d1d1f; margin:0 0 20px;">
            Tambah Pengguna
        </h3>
        <form id="userForm" style="display:flex; flex-direction:column; gap:14px;">
            <input type="hidden" name="id" id="userId">

            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">Nama</label>
                <input type="text"     name="name"     id="userName"     required class="input-field">
            </div>
            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">Email</label>
                <input type="email"    name="email"    id="userEmail"    required class="input-field">
            </div>
            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">Telepon</label>
                <input type="text"     name="phone"    id="userPhone"            class="input-field">
            </div>
            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:4px;">
                    Kata Sandi
                    <span style="font-size:12px; font-weight:400; color:#7a7a7a;">(kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password" id="userPassword"         class="input-field" placeholder="••••••">
            </div>
            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">Role</label>
                <select name="role" id="userRole" required class="input-field">
                    <option value="warga">Warga</option>
                    <option value="admin_instansi">Admin Instansi</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>

            <div style="display:flex; gap:8px; margin-top:8px;">
                <button type="submit" class="btn-pill" style="flex:1; text-align:center;">Simpan</button>
                <button type="button" onclick="closeModal()" class="btn-pill-ghost" style="flex:1; text-align:center;">Batal</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
var _csrfName     = '<?= csrf_token() ?>';
var _csrfCookieN  = '<?= config("Security")->cookieName ?? "csrf_cookie_name" ?>';
var _csrfFallback = '<?= csrf_hash() ?>';
function getCsrf() {
    try {
        var m = document.cookie.match(new RegExp('(?:^|; )' + _csrfCookieN.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + '=([^;]*)'));
        return m ? decodeURIComponent(m[1]) : _csrfFallback;
    } catch(e) { return _csrfFallback; }
}

var roleLabel = { warga: 'Warga', admin_instansi: 'Admin Instansi', super_admin: 'Super Admin' };
var rolePill  = {
    super_admin:    'background:#fee2e2;color:#991b1b;',
    admin_instansi: 'background:#dbeafe;color:#1e3a8a;',
    warga:          'background:#f5f5f7;color:#1d1d1f;'
};

function loadUsers() {
    fetch('/api/admin/list-users')
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var html = '';
            (res.data || []).forEach(function(u) {
                var pill = rolePill[u.role] || rolePill.warga;
                html += '<tr style="border-bottom:1px solid #f0f0f0;">'
                    + '<td style="padding:10px 12px;">' + u.id + '</td>'
                    + '<td style="padding:10px 12px; font-weight:600;">' + u.name + '</td>'
                    + '<td style="padding:10px 12px; color:#7a7a7a;">' + u.email + '</td>'
                    + '<td style="padding:10px 12px; color:#7a7a7a;">' + (u.phone || '-') + '</td>'
                    + '<td style="padding:10px 12px;"><span style="display:inline-block;padding:3px 12px;border-radius:9999px;font-size:12px;font-weight:600;letter-spacing:-0.12px;' + pill + '">'
                    + (roleLabel[u.role] || u.role) + '</span></td>'
                    + '<td style="padding:10px 12px; color:#7a7a7a;">' + new Date(u.created_at).toLocaleDateString('id-ID') + '</td>'
                    + '<td style="padding:10px 12px;"><div style="display:flex;gap:8px;">'
                    + '<button onclick="editUser(\'' + u.id + '\',\'' + u.name.replace(/'/g,"\\'") + '\',\'' + u.email + '\',\'' + (u.phone||'') + '\',\'' + u.role + '\')"'
                    + ' style="color:#0066cc;font-size:13px;background:none;border:none;cursor:pointer;padding:0;"'
                    + ' onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">Edit</button>'
                    + '<button onclick="deleteUser(' + u.id + ')"'
                    + ' style="color:#ef4444;font-size:13px;background:none;border:none;cursor:pointer;padding:0;"'
                    + ' onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">Hapus</button>'
                    + '</div></td></tr>';
            });
            document.getElementById('usersTbody').innerHTML = html ||
                '<tr><td colspan="7" style="padding:20px;text-align:center;color:#7a7a7a;">Tidak ada data</td></tr>';
        })
        .catch(function(e) { console.error('loadUsers:', e); });
}

function showCreateModal() {
    ['userId','userName','userEmail','userPhone','userPassword'].forEach(function(id) { document.getElementById(id).value = ''; });
    document.getElementById('userRole').value = 'warga';
    document.getElementById('userPassword').required = true;
    document.getElementById('userModalTitle').textContent = 'Tambah Pengguna';
    document.getElementById('userModal').style.display = 'flex';
}

function editUser(id, name, email, phone, role) {
    document.getElementById('userId').value       = id;
    document.getElementById('userName').value     = name;
    document.getElementById('userEmail').value    = email;
    document.getElementById('userPhone').value    = phone;
    document.getElementById('userPassword').value = '';
    document.getElementById('userPassword').required = false;
    document.getElementById('userRole').value     = role;
    document.getElementById('userModalTitle').textContent = 'Edit Pengguna';
    document.getElementById('userModal').style.display = 'flex';
}

function closeModal() { document.getElementById('userModal').style.display = 'none'; }

document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var id  = document.getElementById('userId').value;
    var url = id ? '/api/admin/update-user' : '/api/admin/create-user';
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams(new FormData(this)).toString() + '&' + encodeURIComponent(_csrfName) + '=' + encodeURIComponent(getCsrf())
    })
    .then(function(r) { if (!r.ok) throw new Error('SERVER_' + r.status); return r.json(); })
    .then(function(res) { if (res.success) { closeModal(); loadUsers(); } else alert(res.message || 'Gagal menyimpan.'); })
    .catch(function(err) {
        console.error('userForm submit:', err);
        if (err.message && err.message.indexOf('SERVER_403') !== -1) { if (confirm('Sesi habis. Muat ulang?')) location.reload(); }
        else alert('Terjadi kesalahan. Coba lagi.');
    });
});

function deleteUser(id) {
    if (!confirm('Yakin hapus pengguna #' + id + '?')) return;
    fetch('/api/admin/delete-user', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'id=' + encodeURIComponent(id) + '&' + encodeURIComponent(_csrfName) + '=' + encodeURIComponent(getCsrf())
    })
    .then(function(r) { if (!r.ok) throw new Error('SERVER_' + r.status); return r.json(); })
    .then(function(res) { if (res.success) loadUsers(); else alert(res.message || 'Gagal menghapus.'); })
    .catch(function(err) {
        console.error('deleteUser:', err);
        if (err.message && err.message.indexOf('SERVER_403') !== -1) { if (confirm('Sesi habis. Muat ulang?')) location.reload(); }
        else alert('Terjadi kesalahan. Coba lagi.');
    });
}

loadUsers();
</script>
