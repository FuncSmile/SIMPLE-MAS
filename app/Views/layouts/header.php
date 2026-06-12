<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'SIMPEL-MAS') ?></title>
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <!-- Tailwind Play CDN: dynamic endpoint — SRI incompatible. Use `npx tailwindcss` CLI for production. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet 1.9.4 — hashes from official leafletjs.com docs -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- MarkerCluster — generate hash: curl URL | openssl dgst -sha384 -binary | openssl base64 -A -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <!-- DataTables — generate hash via https://www.srihash.org -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:       '#0066cc',
                        'primary-focus': '#0071e3',
                        'primary-dark': '#2997ff',
                        ink:           '#1d1d1f',
                        'ink-muted':   '#333333',
                        'ink-faint':   '#7a7a7a',
                        parchment:     '#f5f5f7',
                        pearl:         '#fafafc',
                        'tile-1':      '#272729',
                        'tile-2':      '#2a2a2c',
                        'tile-3':      '#252527',
                        hairline:      '#e0e0e0',
                        'body-muted':  '#cccccc',
                    }
                }
            }
        }
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Inter', sans-serif;
            font-size: 17px;
            line-height: 1.47;
            letter-spacing: -0.374px;
            color: #1d1d1f;
            background: #ffffff;
            margin: 0;
        }

        /* ── Global button grammar ── */
        .btn-pill {
            display: inline-block;
            background: #0066cc;
            color: #ffffff;
            font-size: 17px;
            font-weight: 400;
            line-height: 1.47;
            letter-spacing: -0.374px;
            padding: 11px 22px;
            border-radius: 9999px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: transform 0.1s ease;
        }
        .btn-pill-ghost {
            display: inline-block;
            background: transparent;
            color: #0066cc;
            font-size: 17px;
            font-weight: 400;
            line-height: 1.47;
            padding: 11px 22px;
            border-radius: 9999px;
            text-decoration: none;
            border: 1px solid #0066cc;
            cursor: pointer;
            transition: transform 0.1s ease;
        }
        .btn-utility {
            display: inline-block;
            background: #1d1d1f;
            color: #ffffff;
            font-size: 14px;
            font-weight: 400;
            letter-spacing: -0.224px;
            line-height: 1.29;
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            transition: transform 0.1s ease;
        }
        /* System-wide active micro-interaction */
        .btn-pill:active, .btn-pill-ghost:active, .btn-utility:active,
        button.btn-pill:active, button.btn-pill-ghost:active, button.btn-utility:active {
            transform: scale(0.95);
        }

        /* ── Focus ring ── */
        :focus-visible {
            outline: 2px solid #0071e3;
            outline-offset: 2px;
        }
        :focus:not(:focus-visible) { outline: none; }

        /* ── Alerts ── */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
            letter-spacing: -0.224px;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-info    { background: #dbeafe; color: #1e3a8a; border: 1px solid #bfdbfe; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        /* ── Card utility ── */
        .card-utility {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 18px;
            padding: 24px;
        }

        /* ── Form input ── */
        .input-field {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 17px;
            font-family: inherit;
            line-height: 1.47;
            color: #1d1d1f;
            background: #ffffff;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .input-field:focus {
            outline: none;
            border-color: #0071e3;
            box-shadow: 0 0 0 3px rgba(0,113,227,0.15);
        }

        /* ── Status pills ── */
        .status-pill {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: -0.12px;
        }
        .status-pending     { background: #fef3c7; color: #92400e; }
        .status-in_progress { background: #dbeafe; color: #1e3a8a; }
        .status-resolved    { background: #d1fae5; color: #065f46; }
        .status-rejected    { background: #fee2e2; color: #991b1b; }

        /* ── Sub-nav frosted glass ── */
        .sub-nav-frosted {
            background: rgba(245, 245, 247, 0.80);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.08);
            height: 52px;
            position: sticky;
            top: 44px;
            z-index: 800; /* above Leaflet's max layer z-index (700) */
            display: flex;
            align-items: center;
        }

        /* ── Mobile menu ── */
        #mobileMenu { display: none; }
        #mobileMenu.is-open { display: block; }

        /* ── Tile section spacing ── */
        .section-tile { padding-top: 80px; padding-bottom: 80px; }

        /* ── Product image shadow (the only shadow in the system) ── */
        .product-shadow { box-shadow: rgba(0,0,0,0.22) 3px 5px 30px 0; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

<!-- ── Global Nav: pure black (#000000), 44px, sticky ── -->
<nav style="background:#000000; height:44px; position:sticky; top:0; z-index:200;" class="flex items-center shrink-0">
    <div class="w-full max-w-[1440px] mx-auto px-5 flex items-center justify-between h-full">

        <!-- Brand -->
        <a href="/" style="color:#fff; font-size:17px; font-weight:600; letter-spacing:-0.374px; line-height:1; text-decoration:none;">
            SIMPEL-MAS
        </a>

        <!-- Desktop Nav Links -->
        <?php $session = service('session'); ?>
        <div class="hidden md:flex items-center gap-5" style="font-size:12px; letter-spacing:-0.12px; line-height:1;">
            <a href="/" style="color:#fff; opacity:.75; text-decoration:none; transition:opacity .15s;"
               onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.75">Beranda</a>
            <?php if ($session->get('isLoggedIn')): ?>
                <?php if ($session->get('role') === 'warga'): ?>
                    <a href="/dashboard" style="color:#fff; opacity:.75; text-decoration:none;"
                       onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.75">Peta</a>
                    <a href="/complaint/create" style="color:#fff; opacity:.75; text-decoration:none;"
                       onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.75">Buat Aduan</a>
                <?php endif; ?>
                <?php if (in_array($session->get('role'), ['admin_instansi', 'super_admin'])): ?>
                    <a href="/admin" style="color:#fff; opacity:.75; text-decoration:none;"
                       onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.75">Panel Admin</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Desktop Right Cluster -->
        <div class="hidden md:flex items-center gap-2 shrink-0">
            <?php if ($session->get('isLoggedIn')): ?>
                <span style="color:#fff; font-size:12px; letter-spacing:-0.12px; opacity:.6;">
                    <?= esc($session->get('name')) ?>
                </span>
                <a href="/logout" class="btn-utility">Keluar</a>
            <?php else: ?>
                <a href="/login" style="color:#fff; font-size:12px; letter-spacing:-0.12px; opacity:.75; text-decoration:none;"
                   onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.75">Masuk</a>
                <a href="/register" class="btn-pill" style="font-size:14px; letter-spacing:-0.224px; padding:7px 15px;">Daftar</a>
            <?php endif; ?>
        </div>

        <!-- Mobile Hamburger -->
        <button id="mobileMenuBtn" class="md:hidden flex items-center justify-center"
                style="background:none; border:none; cursor:pointer; padding:4px; color:#fff;" aria-label="Buka menu">
            <svg width="20" height="16" viewBox="0 0 20 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                <line x1="0" y1="2"  x2="20" y2="2"/>
                <line x1="0" y1="8"  x2="20" y2="8"/>
                <line x1="0" y1="14" x2="20" y2="14"/>
            </svg>
        </button>
    </div>
</nav>

<!-- Mobile Menu -->
<div id="mobileMenu" style="background:#1d1d1f; position:sticky; top:44px; z-index:199;">
    <div style="padding:16px 20px 20px; display:flex; flex-direction:column; gap:12px;">
        <a href="/" style="color:#fff; font-size:17px; letter-spacing:-0.374px; text-decoration:none; opacity:.85;">Beranda</a>
        <?php if ($session->get('isLoggedIn')): ?>
            <?php if ($session->get('role') === 'warga'): ?>
                <a href="/dashboard" style="color:#fff; font-size:17px; text-decoration:none; opacity:.85;">Peta</a>
                <a href="/complaint/create" style="color:#fff; font-size:17px; text-decoration:none; opacity:.85;">Buat Aduan</a>
            <?php endif; ?>
            <?php if (in_array($session->get('role'), ['admin_instansi', 'super_admin'])): ?>
                <a href="/admin" style="color:#fff; font-size:17px; text-decoration:none; opacity:.85;">Panel Admin</a>
            <?php endif; ?>
            <div style="border-top:1px solid rgba(255,255,255,0.15); padding-top:12px; display:flex; align-items:center; gap:12px;">
                <span style="color:#fff; font-size:12px; letter-spacing:-0.12px; opacity:.5;"><?= esc($session->get('name')) ?></span>
                <a href="/logout" class="btn-utility">Keluar</a>
            </div>
        <?php else: ?>
            <div style="border-top:1px solid rgba(255,255,255,0.15); padding-top:12px; display:flex; gap:8px; align-items:center;">
                <a href="/login"    style="color:#fff; font-size:17px; text-decoration:none; opacity:.85;">Masuk</a>
                <span style="color:rgba(255,255,255,0.25); font-size:17px;">|</span>
                <a href="/register" style="color:#fff; font-size:17px; text-decoration:none; opacity:.85;">Daftar</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('mobileMenuBtn').addEventListener('click', function () {
    document.getElementById('mobileMenu').classList.toggle('is-open');
});
</script>

<main class="flex-1">
<?php if ($flash = session()->getFlashdata('success')): ?>
    <div style="max-width:980px; margin:0 auto; padding:16px 20px 0;">
        <div class="alert alert-success"><?= esc($flash) ?></div>
    </div>
<?php endif; ?>
<?php if ($flash = session()->getFlashdata('error')): ?>
    <div style="max-width:980px; margin:0 auto; padding:16px 20px 0;">
        <div class="alert alert-error"><?= esc($flash) ?></div>
    </div>
<?php endif; ?>
<?php if ($flash = session()->getFlashdata('warning')): ?>
    <div style="max-width:980px; margin:0 auto; padding:16px 20px 0;">
        <div class="alert alert-warning"><?= esc($flash) ?></div>
    </div>
<?php endif; ?>
