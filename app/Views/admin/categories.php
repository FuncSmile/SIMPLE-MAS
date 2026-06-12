<?php $GLOBALS['ci_no_footer'] = true; ?>
<!-- ── MANAJEMEN KATEGORI ── -->
<style>
@media (max-width: 767px) {
    #adminShell { display: block !important; height: auto !important; overflow: visible !important; }
    #adminMain  { height: auto !important; overflow: visible !important; }
    #catPad     { padding: 20px 16px 80px !important; }
}
@media (min-width: 768px) and (max-width: 1023px) {
    #catPad { padding: 24px 20px 56px !important; }
}
</style>
<div id="adminShell" style="display:flex; height:calc(100vh - 44px); overflow:hidden;">

    <?= view('admin/sidebar', ['currentPage' => 'categories']) ?>

    <div id="adminMain" style="flex:1; min-width:0; overflow-y:auto; background:#f5f5f7;">
    <div id="catPad" style="max-width:1100px; margin:0 auto; padding:32px 32px 56px;">

        <div style="margin-bottom:28px;">
            <p style="font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#0066cc; margin:0 0 4px;">Admin Panel</p>
            <h1 style="font-size:34px; font-weight:700; line-height:1.1; letter-spacing:-0.5px; color:#1d1d1f; margin:0;">Manajemen Kategori</h1>
        </div>

        <div class="card-utility" style="padding:0; overflow:hidden;">
            <!-- Card header -->
            <div style="padding:16px 24px; border-bottom:1px solid #e0e0e0; display:flex; align-items:center; justify-content:space-between; background:#ffffff;">
                <p style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f; margin:0;">Daftar Kategori</p>
                <button onclick="showCatModal()" class="btn-pill" style="font-size:14px; letter-spacing:-0.224px; padding:8px 16px;">
                    + Tambah Kategori
                </button>
            </div>
            <!-- Table -->
            <div style="padding:0 24px 16px; overflow-x:auto; background:#ffffff;">
                <table style="width:100%; font-size:14px; letter-spacing:-0.224px; color:#1d1d1f; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f5f5f7; text-align:left;">
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">ID</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Nama</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Deskripsi</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Instansi</th>
                            <th style="padding:10px 12px; font-weight:600; border-bottom:1px solid #e0e0e0;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTbody"></tbody>
                </table>
            </div>
        </div>
    </div><!-- /max-width -->
    </div><!-- /main scroll -->

</div><!-- /shell -->

<!-- Modal: Create / Edit Category -->
<div id="catModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:500; align-items:center; justify-content:center; padding:20px;">
    <div class="card-utility" style="width:100%; max-width:440px;">
        <h3 id="catModalTitle" style="font-size:21px; font-weight:600; letter-spacing:0.231px; color:#1d1d1f; margin:0 0 20px;">
            Tambah Kategori
        </h3>
        <form id="catForm" style="display:flex; flex-direction:column; gap:14px;">
            <input type="hidden" name="id" id="catId">

            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">Nama Kategori</label>
                <input type="text" name="name" id="catName" required class="input-field">
            </div>
            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">Deskripsi</label>
                <textarea name="description" id="catDescription" rows="2" class="input-field" style="resize:vertical;"></textarea>
            </div>
            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">Instansi Penanggung Jawab</label>
                <input type="text" name="agency" id="catAgency" class="input-field">
            </div>

            <div style="display:flex; gap:8px; margin-top:8px;">
                <button type="submit" class="btn-pill" style="flex:1; text-align:center;">Simpan</button>
                <button type="button" onclick="closeCatModal()" class="btn-pill-ghost" style="flex:1; text-align:center;">Batal</button>
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

function loadCategories() {
    fetch('/api/admin/list-categories')
        .then(function(r) { return r.json(); })
        .then(function(res) {
            var html = '';
            (res.data || []).forEach(function(c) {
                html += '<tr style="border-bottom:1px solid #f0f0f0;">'
                    + '<td style="padding:10px 12px;">' + c.id + '</td>'
                    + '<td style="padding:10px 12px; font-weight:600;">' + c.name + '</td>'
                    + '<td style="padding:10px 12px; color:#7a7a7a;">' + (c.description || '-') + '</td>'
                    + '<td style="padding:10px 12px; color:#7a7a7a;">' + (c.agency || '-') + '</td>'
                    + '<td style="padding:10px 12px;"><div style="display:flex;gap:8px;">'
                    + '<button onclick="editCat(' + c.id + ',\'' + c.name.replace(/'/g,"\\'") + '\',\'' + (c.description||'').replace(/'/g,"\\'") + '\',\'' + (c.agency||'').replace(/'/g,"\\'") + '\')"'
                    + ' style="color:#0066cc;font-size:13px;background:none;border:none;cursor:pointer;padding:0;"'
                    + ' onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">Edit</button>'
                    + '<button onclick="deleteCat(' + c.id + ')"'
                    + ' style="color:#ef4444;font-size:13px;background:none;border:none;cursor:pointer;padding:0;"'
                    + ' onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">Hapus</button>'
                    + '</div></td></tr>';
            });
            document.getElementById('categoriesTbody').innerHTML = html ||
                '<tr><td colspan="5" style="padding:20px;text-align:center;color:#7a7a7a;">Tidak ada data</td></tr>';
        })
        .catch(function(e) { console.error('loadCategories:', e); });
}

function showCatModal() {
    ['catId','catName','catDescription','catAgency'].forEach(function(id) { document.getElementById(id).value = ''; });
    document.getElementById('catModalTitle').textContent = 'Tambah Kategori';
    document.getElementById('catModal').style.display = 'flex';
}

function editCat(id, name, desc, agency) {
    document.getElementById('catId').value          = id;
    document.getElementById('catName').value        = name;
    document.getElementById('catDescription').value = desc;
    document.getElementById('catAgency').value      = agency;
    document.getElementById('catModalTitle').textContent = 'Edit Kategori';
    document.getElementById('catModal').style.display = 'flex';
}

function closeCatModal() { document.getElementById('catModal').style.display = 'none'; }

document.getElementById('catForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var id  = document.getElementById('catId').value;
    var url = id ? '/api/admin/update-category' : '/api/admin/create-category';
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams(new FormData(this)).toString() + '&' + encodeURIComponent(_csrfName) + '=' + encodeURIComponent(getCsrf())
    })
    .then(function(r) { if (!r.ok) throw new Error('SERVER_' + r.status); return r.json(); })
    .then(function(res) { if (res.success) { closeCatModal(); loadCategories(); } else alert(res.message || 'Gagal menyimpan.'); })
    .catch(function(err) {
        console.error('catForm submit:', err);
        if (err.message && err.message.indexOf('SERVER_403') !== -1) { if (confirm('Sesi habis. Muat ulang?')) location.reload(); }
        else alert('Terjadi kesalahan. Coba lagi.');
    });
});

function deleteCat(id) {
    if (!confirm('Yakin hapus kategori #' + id + '?')) return;
    fetch('/api/admin/delete-category', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'id=' + encodeURIComponent(id) + '&' + encodeURIComponent(_csrfName) + '=' + encodeURIComponent(getCsrf())
    })
    .then(function(r) { if (!r.ok) throw new Error('SERVER_' + r.status); return r.json(); })
    .then(function(res) { if (res.success) loadCategories(); else alert(res.message || 'Gagal menghapus.'); })
    .catch(function(err) {
        console.error('deleteCat:', err);
        if (err.message && err.message.indexOf('SERVER_403') !== -1) { if (confirm('Sesi habis. Muat ulang?')) location.reload(); }
        else alert('Terjadi kesalahan. Coba lagi.');
    });
}

loadCategories();
</script>
