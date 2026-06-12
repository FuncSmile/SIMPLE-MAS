<?php
$currentPage = $currentPage ?? 'dashboard';
$role        = session()->get('role') ?? 'admin';
$userName    = session()->get('name') ?? 'Admin';
$initial     = strtoupper(mb_substr($userName, 0, 1));

$nav = [
    ['id' => 'dashboard', 'label' => 'Dashboard',      'emoji' => '📊', 'href' => '/admin'],
    ['id' => 'datatable', 'label' => 'Data Pengaduan', 'emoji' => '📋', 'href' => '/admin/datatable'],
    ['id' => 'analytics', 'label' => 'Analitik',       'emoji' => '📈', 'href' => '/admin/analytics'],
];
if ($role === 'super_admin') {
    $nav[] = ['id' => 'users',      'label' => 'Pengguna', 'emoji' => '👥', 'href' => '/admin/users'];
    $nav[] = ['id' => 'categories', 'label' => 'Kategori', 'emoji' => '🏷️', 'href' => '/admin/categories'];
}
?>

<!-- ── Mobile Sidebar Overlay Backdrop ── -->
<div id="sidebarOverlay"
     onclick="closeSidebar()"
     style="display:none; position:fixed; inset:0; top:44px; background:rgba(0,0,0,0.55); z-index:350; backdrop-filter:blur(2px);"></div>

<aside id="adminSidebar" style="
    width:240px; flex-shrink:0;
    background:#111113;
    height:calc(100vh - 44px);
    position:sticky; top:44px;
    display:flex; flex-direction:column;
    border-right:1px solid rgba(255,255,255,0.055);
    overflow-y:auto;
    transition: transform 0.25s cubic-bezier(0.4,0,0.2,1);
    z-index:400;
">

    <!-- Brand + mobile close -->
    <div style="padding:18px 20px 16px; border-bottom:1px solid rgba(255,255,255,0.07); flex-shrink:0; display:flex; align-items:center; justify-content:space-between;">
        <div>
            <p style="font-size:10px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:#6ba3e0; margin:0 0 3px; line-height:1;">Admin Panel</p>
            <p style="font-size:17px; font-weight:600; color:#ffffff; margin:0; letter-spacing:-0.374px; line-height:1.3;">SIMPEL-MAS</p>
        </div>
        <!-- Close button (mobile only) -->
        <button id="sidebarClose" onclick="closeSidebar()"
                style="display:none; background:rgba(255,255,255,0.07); border:none; color:rgba(255,255,255,0.7); cursor:pointer; border-radius:6px; padding:6px 8px; font-size:18px; line-height:1; flex-shrink:0;"
                aria-label="Tutup menu">✕</button>
    </div>

    <!-- Navigation -->
    <nav style="padding:14px 10px; flex:1; display:flex; flex-direction:column; gap:1px;">

        <p style="font-size:10px; font-weight:700; letter-spacing:0.8px; text-transform:uppercase; color:rgba(255,255,255,0.22); margin:0 0 8px 10px; line-height:1;">MENU</p>

        <?php foreach ($nav as $item):
            $active   = ($currentPage === $item['id']);
            $bgStyle  = $active ? 'background:rgba(0,102,204,0.18);' : '';
            $colStyle = $active ? 'color:#ffffff;' : 'color:rgba(255,255,255,0.52);';
            $border   = $active ? 'border-left:3px solid #0066cc;' : 'border-left:3px solid transparent;';
            $weight   = $active ? 'font-weight:600;' : 'font-weight:400;';
            $hover    = !$active
                ? 'onmouseover="this.style.background=\'rgba(255,255,255,0.07)\';this.style.color=\'rgba(255,255,255,0.88)\'"'
                  . ' onmouseout="this.style.background=\'transparent\';this.style.color=\'rgba(255,255,255,0.52)\'"'
                : '';
        ?>
        <a href="<?= $item['href'] ?>"
            onclick="closeSidebar()"
            style="display:flex;align-items:center;gap:10px;padding:10px 10px 10px 12px;border-radius:8px;text-decoration:none;font-size:14px;letter-spacing:-0.224px;line-height:1.3;transition:background 0.12s,color 0.12s;<?= $bgStyle.$colStyle.$border.$weight ?>"
            <?= $hover ?>>
            <span style="font-size:16px;line-height:1;flex-shrink:0;"><?= $item['emoji'] ?></span>
            <?= esc($item['label']) ?>
            <?php if ($active): ?>
                <span style="margin-left:auto;width:5px;height:5px;border-radius:50%;background:#0066cc;flex-shrink:0;"></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>

    </nav>

    <!-- User info + logout -->
    <div style="padding:14px 20px 18px; border-top:1px solid rgba(255,255,255,0.07); flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <div style="
                width:32px;height:32px;border-radius:50%;
                background:#0066cc;
                display:flex;align-items:center;justify-content:center;
                font-size:13px;font-weight:700;color:#fff;flex-shrink:0;
                letter-spacing:0;
            "><?= esc($initial) ?></div>
            <div style="min-width:0;">
                <p style="font-size:13px;font-weight:600;color:#ffffff;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;letter-spacing:-0.12px;line-height:1.3;"><?= esc($userName) ?></p>
                <p style="font-size:11px;color:rgba(255,255,255,0.38);margin:0;text-transform:capitalize;letter-spacing:0.2px;line-height:1.3;"><?= esc(str_replace('_', ' ', $role)) ?></p>
            </div>
        </div>
        <a href="/logout"
            style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,0.38);font-size:13px;letter-spacing:-0.12px;text-decoration:none;transition:color 0.12s;line-height:1.3;"
            onmouseover="this.style.color='#ef4444'"
            onmouseout="this.style.color='rgba(255,255,255,0.38)'">
            <span style="font-size:15px;">⏻</span> Keluar
        </a>
    </div>

</aside>

<!-- ── Mobile Hamburger (floating, bottom-right) ── -->
<button id="sidebarToggle" onclick="toggleSidebar()"
        style="display:none; position:fixed; bottom:24px; right:20px; z-index:380;
               width:50px; height:50px; border-radius:50%;
               background:#0066cc; color:#ffffff; border:none;
               font-size:22px; cursor:pointer;
               align-items:center; justify-content:center;
               box-shadow:0 4px 16px rgba(0,102,204,0.45);
               transition:transform 0.1s;"
        aria-label="Buka menu">
    ☰
</button>

<style>
@media (max-width: 767px) {
    #adminSidebar {
        position: fixed !important;
        top: 44px;
        left: 0;
        transform: translateX(-100%);
        height: calc(100vh - 44px) !important;
        z-index: 400;
    }
    #adminSidebar.sidebar-open {
        transform: translateX(0);
    }
    #sidebarClose  { display: flex !important; }
    #sidebarToggle { display: flex !important; }
    #sidebarOverlay.sidebar-open { display: block !important; }
}
</style>

<script>
function toggleSidebar() {
    var s = document.getElementById('adminSidebar');
    var o = document.getElementById('sidebarOverlay');
    s.classList.toggle('sidebar-open');
    o.classList.toggle('sidebar-open');
}
function closeSidebar() {
    document.getElementById('adminSidebar').classList.remove('sidebar-open');
    document.getElementById('sidebarOverlay').classList.remove('sidebar-open');
}
</script>
