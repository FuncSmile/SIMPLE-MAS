<!-- D3.js for globe visualization -->
<script src="https://cdn.jsdelivr.net/npm/d3@7.9.0/dist/d3.min.js" integrity="sha384-CjloA8y00+1SDAUkjs099PVfnY2KmDC2BZnws9kh8D/lX1s46w6EPhpXdqMfjK6i" crossorigin="anonymous"></script>

<style>
/* ── Landing page responsive overrides ── */
@media (max-width: 767px) {
    /* Hero: reduce bottom padding & button gap */
    #hero-content { padding: 0 20px 48px !important; }
    #hero-btns    { gap: 10px !important; }
    #hero-btns a  { font-size: 15px !important; padding: 11px 22px !important; }

    /* Sections: reduce padding */
    .home-section { padding: 52px 0 !important; }
    .home-inner   { padding: 0 20px !important; }
}
@media (min-width: 768px) and (max-width: 1023px) {
    #hero-content { padding: 0 28px 56px !important; }
    .home-inner   { padding: 0 28px !important; }
}
</style>
@keyframes scrollDot {
    0%,100% { transform:translateX(-50%) translateY(0); opacity:1; }
    50%      { transform:translateX(-50%) translateY(10px); opacity:0.3; }
}
@keyframes heroFadeUp {
    from { opacity:0; transform:translateY(28px); }
    to   { opacity:1; transform:translateY(0); }
}
.hero-text { animation:heroFadeUp 0.9s cubic-bezier(0.22,1,0.36,1) 0.25s both; }
</style>

<!-- ═══════════════════════════════════════════════════════
     HERO — 100vh, dark canvas, D3 globe as background
     Globe: 70% of container, centered
     Content: absolute overlay, pointer-events:none (buttons: auto)
     ═══════════════════════════════════════════════════════ -->
<section id="hero" style="position:relative; height:100vh; background:#1a1a1c; overflow:hidden;">

    <!-- Globe: 70% of container, centered, z-index 2 -->
    <div id="globe-container" style="
        position:absolute;
        top:50%; left:50%;
        transform:translate(-50%, -50%);
        width:min(70vh, calc(100vw - 40px));
        height:min(70vh, calc(100vw - 40px));
        z-index:2;
    ">
        <canvas id="globe-canvas" style="width:100%; height:100%; display:block; cursor:grab; border-radius:50%;"></canvas>
        <div id="globe-loading" style="
            position:absolute; inset:0;
            display:flex; align-items:center; justify-content:center;
            background:rgba(0,0,0,0.45); border-radius:50%;
        ">
            <span style="color:#cccccc; font-size:14px; letter-spacing:-0.224px;">Memuat globe...</span>
        </div>
    </div>

    <!-- Radial scrim: darkens edges so globe "floats", center stays clear -->
    <div style="
        position:absolute; inset:0; z-index:3; pointer-events:none;
        background:radial-gradient(ellipse 72% 72% at 50% 50%,
            transparent 38%,
            rgba(26,26,28,0.55) 65%,
            rgba(26,26,28,0.92) 100%);
    "></div>

    <!-- Bottom gradient scrim: ensures text is readable over the globe -->
    <div style="
        position:absolute; inset:0; z-index:4; pointer-events:none;
        background:linear-gradient(to top,
            rgba(26,26,28,1)    0%,
            rgba(26,26,28,0.85) 18%,
            rgba(26,26,28,0.45) 38%,
            transparent         60%);
    "></div>

    <!-- Content overlay — pointer-events:none so globe stays interactive -->
    <div id="hero-content" class="hero-text" style="
        position:absolute; inset:0; z-index:10;
        display:flex; flex-direction:column;
        align-items:center; justify-content:flex-end;
        padding:0 40px 68px;
        pointer-events:none;
        text-align:center;
    ">
        <span style="
            display:inline-block; margin-bottom:20px;
            background:rgba(0,102,204,0.18); border:1px solid rgba(0,102,204,0.42);
            border-radius:9999px; padding:5px 18px;
            font-size:11px; font-weight:700; letter-spacing:1px;
            color:#6ba3e0; text-transform:uppercase;
        ">Sistem Pengaduan Masyarakat</span>

        <h1 style="
            font-size:clamp(44px,7vw,76px); font-weight:700;
            line-height:1.03; letter-spacing:-0.5px;
            color:#ffffff; margin:0 0 16px; max-width:800px;
        ">SIMPEL-MAS</h1>

        <p style="
            font-size:clamp(17px,2vw,22px); font-weight:400;
            line-height:1.47; letter-spacing:-0.374px;
            color:rgba(255,255,255,0.68); max-width:500px; margin:0 0 40px;
        ">Pengaduan Cerdas. Penanganan Transparan.<br>
        Bersama kita bangun lingkungan yang lebih baik.</p>

        <!-- Buttons: pointer-events:auto so they remain clickable -->
        <div id="hero-btns" style="display:flex; gap:14px; flex-wrap:wrap; justify-content:center; pointer-events:auto; margin-bottom:36px;">
            <a href="/register" class="btn-pill" style="
                font-size:17px; padding:13px 30px;
                box-shadow:0 0 28px rgba(0,102,204,0.45);
            ">Daftar Sekarang</a>
            <a href="/login" style="
                display:inline-block; text-decoration:none;
                background:rgba(255,255,255,0.1);
                -webkit-backdrop-filter:blur(12px); backdrop-filter:blur(12px);
                color:#ffffff; border:1px solid rgba(255,255,255,0.28);
                border-radius:9999px; padding:13px 30px; font-size:17px;
                transition:background 0.2s;
            "
            onmouseover="this.style.background='rgba(255,255,255,0.18)'"
            onmouseout="this.style.background='rgba(255,255,255,0.1)'">Masuk</a>
        </div>
    </div>

</section>

<!-- Globe init script -->
<script>
(function () {
    function initGlobe() {
        if (typeof d3 === 'undefined') { setTimeout(initGlobe, 100); return; }

        var canvas    = document.getElementById('globe-canvas');
        var container = document.getElementById('globe-container');
        if (!canvas || !container) return;
        var ctx = canvas.getContext('2d');
        if (!ctx) return;

        var size   = container.offsetWidth;   /* square: width = height = 70% of hero */
        var radius = size / 2.05;
        var dpr    = window.devicePixelRatio || 1;

        canvas.width  = size * dpr;
        canvas.height = size * dpr;
        canvas.style.width  = size + 'px';
        canvas.style.height = size + 'px';
        ctx.scale(dpr, dpr);

        var proj = d3.geoOrthographic()
            .scale(radius)
            .translate([size / 2, size / 2])
            .clipAngle(90);

        var path = d3.geoPath().projection(proj).context(ctx);

        /* ── point-in-polygon helpers ── */
        function ptInRing(pt, ring) {
            var x = pt[0], y = pt[1], inside = false;
            for (var i = 0, j = ring.length - 1; i < ring.length; j = i++) {
                var xi = ring[i][0], yi = ring[i][1];
                var xj = ring[j][0], yj = ring[j][1];
                if ((yi > y) !== (yj > y) && x < (xj - xi) * (y - yi) / (yj - yi) + xi)
                    inside = !inside;
            }
            return inside;
        }
        function ptInFeature(pt, f) {
            var g = f.geometry;
            if (g.type === 'Polygon') {
                if (!ptInRing(pt, g.coordinates[0])) return false;
                for (var i = 1; i < g.coordinates.length; i++)
                    if (ptInRing(pt, g.coordinates[i])) return false;
                return true;
            }
            if (g.type === 'MultiPolygon') {
                for (var p = 0; p < g.coordinates.length; p++) {
                    var poly = g.coordinates[p];
                    if (ptInRing(pt, poly[0])) {
                        var hole = false;
                        for (var h = 1; h < poly.length; h++)
                            if (ptInRing(pt, poly[h])) { hole = true; break; }
                        if (!hole) return true;
                    }
                }
            }
            return false;
        }
        function genDots(feature) {
            var dots = [], b = d3.geoBounds(feature), step = 1.3;
            for (var lng = b[0][0]; lng <= b[1][0]; lng += step)
                for (var lat = b[0][1]; lat <= b[1][1]; lat += step)
                    if (ptInFeature([lng, lat], feature)) dots.push([lng, lat]);
            return dots;
        }

        var allDots = [], land = null;

        function render() {
            ctx.clearRect(0, 0, size, size);
            var sc = proj.scale(), sf = sc / radius;

            /* ocean sphere */
            ctx.beginPath();
            ctx.arc(size / 2, size / 2, sc, 0, 2 * Math.PI);
            ctx.fillStyle = '#06060a';
            ctx.fill();
            ctx.strokeStyle = 'rgba(0,102,204,0.5)';
            ctx.lineWidth = 1.8 * sf;
            ctx.stroke();

            if (!land) return;

            /* graticule */
            ctx.beginPath();
            path(d3.geoGraticule()());
            ctx.strokeStyle = 'rgba(0,102,204,0.25)';
            ctx.lineWidth = 0.4 * sf;
            ctx.stroke();

            /* country outlines */
            ctx.beginPath();
            land.features.forEach(function (f) { path(f); });
            ctx.strokeStyle = 'rgba(0,102,204,0.65)';
            ctx.lineWidth = 0.9 * sf;
            ctx.stroke();

            /* dots */
            allDots.forEach(function (dot) {
                var pr = proj(dot);
                if (pr && pr[0] >= 0 && pr[0] <= size && pr[1] >= 0 && pr[1] <= size) {
                    ctx.beginPath();
                    ctx.arc(pr[0], pr[1], 1.4 * sf, 0, 2 * Math.PI);
                    ctx.fillStyle = 'rgba(0,102,204,0.75)';
                    ctx.fill();
                }
            });
        }

        /* auto-rotation — starts showing Indonesia area */
        var rot = [-118, 5], autoRotate = true;
        d3.timer(function () {
            if (autoRotate) { rot[0] -= 0.22; proj.rotate(rot); render(); }
        });

        function resumeRotate() {
            setTimeout(function () { autoRotate = true; }, 1400);
        }

        /* ── mouse drag ── */
        canvas.addEventListener('mousedown', function (e) {
            autoRotate = false;
            canvas.style.cursor = 'grabbing';
            var sx = e.clientX, sy = e.clientY, sr = rot.slice();
            function move(ev) {
                rot[0] = sr[0] + (ev.clientX - sx) * 0.5;
                rot[1] = Math.max(-90, Math.min(90, sr[1] - (ev.clientY - sy) * 0.5));
                proj.rotate(rot); render();
            }
            function up() {
                canvas.style.cursor = 'grab';
                document.removeEventListener('mousemove', move);
                document.removeEventListener('mouseup', up);
                resumeRotate();
            }
            document.addEventListener('mousemove', move);
            document.addEventListener('mouseup', up);
        });

        /* ── wheel zoom ── */
        canvas.addEventListener('wheel', function (e) {
            e.preventDefault();
            var ns = Math.max(radius * 0.55, Math.min(radius * 2.8,
                proj.scale() * (e.deltaY > 0 ? 0.92 : 1.08)));
            proj.scale(ns); render();
        }, { passive: false });

        /* ── touch ── */
        canvas.addEventListener('touchstart', function (e) {
            autoRotate = false;
            var t = e.touches[0]; canvas._tx = t.clientX; canvas._ty = t.clientY;
        }, { passive: true });
        canvas.addEventListener('touchmove', function (e) {
            e.preventDefault();
            var t = e.touches[0];
            rot[0] += (t.clientX - canvas._tx) * 0.5;
            rot[1] = Math.max(-90, Math.min(90, rot[1] - (t.clientY - canvas._ty) * 0.5));
            proj.rotate(rot); render();
            canvas._tx = t.clientX; canvas._ty = t.clientY;
        }, { passive: false });
        canvas.addEventListener('touchend', resumeRotate);

        /* ── load geodata ── */
        fetch('https://raw.githubusercontent.com/martynafford/natural-earth-geojson/refs/heads/master/110m/physical/ne_110m_land.json')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                land = data;
                data.features.forEach(function (f) {
                    genDots(f).forEach(function (d) { allDots.push(d); });
                });
                var el = document.getElementById('globe-loading');
                if (el) el.style.display = 'none';
                render();
            })
            .catch(function () {
                var el = document.getElementById('globe-loading');
                if (el) el.innerHTML = '<span style="color:#fca5a5;font-size:13px;letter-spacing:-0.12px;">Gagal memuat globe</span>';
            });
    }

    if (document.readyState === 'loading')
        document.addEventListener('DOMContentLoaded', initGlobe);
    else
        initGlobe();
})();
</script>


<!-- ═══════════════════════════════════════════════════════
     PROBLEM STATEMENT — white
     ═══════════════════════════════════════════════════════ -->
<section class="home-section" style="background:#ffffff; padding:80px 0;">
    <div class="home-inner" style="max-width:980px; margin:0 auto; padding:0 40px;">

        <p style="font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#0066cc; margin:0 0 12px;">
            Mengapa SIMPEL-MAS?
        </p>
        <h2 style="font-size:clamp(32px,4vw,48px); font-weight:600; line-height:1.08; letter-spacing:-0.2px; color:#1d1d1f; margin:0 0 56px; max-width:640px;">
            Pengaduan tanpa sistem yang jelas tidak pernah selesai.
        </h2>

        <div class="grid md:grid-cols-2 gap-8">

            <div style="border-left:3px solid #0066cc; padding-left:24px;">
                <h3 style="font-size:21px; font-weight:600; letter-spacing:0.231px; color:#1d1d1f; margin:0 0 8px;">
                    Laporan Duplikat &amp; Tersebar
                </h3>
                <p style="font-size:17px; font-weight:400; line-height:1.47; letter-spacing:-0.374px; color:#6e6e73; margin:0;">
                    Warga melapor di berbagai kanal berbeda. Hasilnya: duplikasi yang membingungkan admin dan memperlambat penanganan.
                </p>
            </div>

            <div style="border-left:3px solid #0066cc; padding-left:24px;">
                <h3 style="font-size:21px; font-weight:600; letter-spacing:0.231px; color:#1d1d1f; margin:0 0 8px;">
                    Proses Tidak Transparan
                </h3>
                <p style="font-size:17px; font-weight:400; line-height:1.47; letter-spacing:-0.374px; color:#6e6e73; margin:0;">
                    Pelapor tidak tahu laporan mereka diproses atau tidak. Tidak ada bukti nyata penyelesaian yang bisa diverifikasi publik.
                </p>
            </div>

            <div style="border-left:3px solid #0066cc; padding-left:24px;">
                <h3 style="font-size:21px; font-weight:600; letter-spacing:0.231px; color:#1d1d1f; margin:0 0 8px;">
                    Prioritas Tidak Terukur
                </h3>
                <p style="font-size:17px; font-weight:400; line-height:1.47; letter-spacing:-0.374px; color:#6e6e73; margin:0;">
                    Admin kesulitan menentukan masalah mana yang harus ditangani lebih dulu tanpa data agregat yang memadai.
                </p>
            </div>

            <div style="border-left:3px solid #0066cc; padding-left:24px;">
                <h3 style="font-size:21px; font-weight:600; letter-spacing:0.231px; color:#1d1d1f; margin:0 0 8px;">
                    Lokasi Tidak Akurat
                </h3>
                <p style="font-size:17px; font-weight:400; line-height:1.47; letter-spacing:-0.374px; color:#6e6e73; margin:0;">
                    Deskripsi lokasi manual sering tidak tepat, mempersulit petugas lapangan menemukan titik masalah yang dimaksud.
                </p>
            </div>

        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     SOLUTION FEATURES — parchment
     ═══════════════════════════════════════════════════════ -->
<section class="home-section" style="background:#f5f5f7; padding:80px 0;">
    <div class="home-inner" style="max-width:980px; margin:0 auto; padding:0 40px;">

        <p style="font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#0066cc; margin:0 0 12px; text-align:center;">
            Solusi
        </p>
        <h2 style="font-size:clamp(32px,4vw,48px); font-weight:600; line-height:1.08; letter-spacing:-0.2px; color:#1d1d1f; text-align:center; margin:0 0 64px;">
            Semua yang Anda butuhkan,<br>dalam satu platform.
        </h2>

        <div class="grid md:grid-cols-2 gap-5">

            <div class="card-utility" style="display:flex; align-items:flex-start; gap:20px;">
                <div style="flex-shrink:0; width:48px; height:48px; background:#0066cc; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; line-height:1;">&#128506;</div>
                <div>
                    <h3 style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f; margin:0 0 6px;">Peta Interaktif</h3>
                    <p style="font-size:14px; font-weight:400; line-height:1.43; letter-spacing:-0.224px; color:#7a7a7a; margin:0;">
                        Visualisasikan semua pengaduan di peta. Filter berdasarkan kategori, status, atau wilayah secara langsung.
                    </p>
                </div>
            </div>

            <div class="card-utility" style="display:flex; align-items:flex-start; gap:20px;">
                <div style="flex-shrink:0; width:48px; height:48px; background:#0066cc; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; line-height:1;">&#128204;</div>
                <div>
                    <h3 style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f; margin:0 0 6px;">Lokasi GPS Otomatis</h3>
                    <p style="font-size:14px; font-weight:400; line-height:1.43; letter-spacing:-0.224px; color:#7a7a7a; margin:0;">
                        Koordinat terdeteksi otomatis. Pelapor cukup foto dan deskripsikan — lokasi presisi sudah tercatat.
                    </p>
                </div>
            </div>

            <div class="card-utility" style="display:flex; align-items:flex-start; gap:20px;">
                <div style="flex-shrink:0; width:48px; height:48px; background:#0066cc; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; line-height:1;">&#128269;</div>
                <div>
                    <h3 style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f; margin:0 0 6px;">Deteksi Duplikat 50m</h3>
                    <p style="font-size:14px; font-weight:400; line-height:1.43; letter-spacing:-0.224px; color:#7a7a7a; margin:0;">
                        Sistem otomatis mendeteksi laporan serupa dalam radius 50 meter dan menyarankan upvote daripada duplikasi.
                    </p>
                </div>
            </div>

            <div class="card-utility" style="display:flex; align-items:flex-start; gap:20px;">
                <div style="flex-shrink:0; width:48px; height:48px; background:#0066cc; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; line-height:1;">&#128077;</div>
                <div>
                    <h3 style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f; margin:0 0 6px;">Upvote &amp; Prioritas</h3>
                    <p style="font-size:14px; font-weight:400; line-height:1.43; letter-spacing:-0.224px; color:#7a7a7a; margin:0;">
                        Warga mendukung laporan relevan. Laporan dengan upvote terbanyak naik prioritas penanganan instansi.
                    </p>
                </div>
            </div>

            <div class="card-utility" style="display:flex; align-items:flex-start; gap:20px;">
                <div style="flex-shrink:0; width:48px; height:48px; background:#0066cc; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; line-height:1;">&#128065;</div>
                <div>
                    <h3 style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f; margin:0 0 6px;">Transparansi Foto</h3>
                    <p style="font-size:14px; font-weight:400; line-height:1.43; letter-spacing:-0.224px; color:#7a7a7a; margin:0;">
                        Foto sebelum &amp; sesudah perbaikan tersimpan dan dapat dilihat publik. Bukti konkret yang tidak bisa dipalsukan.
                    </p>
                </div>
            </div>

            <div class="card-utility" style="display:flex; align-items:flex-start; gap:20px;">
                <div style="flex-shrink:0; width:48px; height:48px; background:#0066cc; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; line-height:1;">&#128200;</div>
                <div>
                    <h3 style="font-size:17px; font-weight:600; letter-spacing:-0.374px; color:#1d1d1f; margin:0 0 6px;">Analitik &amp; Heatmap</h3>
                    <p style="font-size:14px; font-weight:400; line-height:1.43; letter-spacing:-0.224px; color:#7a7a7a; margin:0;">
                        Admin melihat tren kategori, grafik bulanan, dan heatmap konsentrasi masalah untuk pengambilan keputusan berbasis data.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     USER ROLES — dark tile
     ═══════════════════════════════════════════════════════ -->
<section class="home-section" style="background:#272729; padding:80px 0;">
    <div class="home-inner" style="max-width:980px; margin:0 auto; padding:0 40px;">

        <p style="font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#6ba3e0; margin:0 0 12px; text-align:center;">
            Platform untuk Semua
        </p>
        <h2 style="font-size:clamp(32px,4vw,48px); font-weight:600; line-height:1.08; letter-spacing:-0.2px; color:#ffffff; text-align:center; margin:0 0 64px;">
            Satu platform. Tiga peran.
        </h2>

        <div class="grid md:grid-cols-3 gap-6">

            <!-- Warga -->
            <div style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:18px; padding:28px 24px;">
                <div style="font-size:34px; margin-bottom:16px; line-height:1;">&#128100;</div>
                <h3 style="font-size:21px; font-weight:600; letter-spacing:0.231px; color:#ffffff; margin:0 0 16px;">Warga</h3>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px;">
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#0066cc; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Daftar akun &amp; masuk</span>
                    </li>
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#0066cc; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Buat laporan dengan GPS &amp; foto</span>
                    </li>
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#0066cc; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Upvote laporan lain yang relevan</span>
                    </li>
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#0066cc; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Pantau status &amp; riwayat laporan</span>
                    </li>
                </ul>
            </div>

            <!-- Admin Instansi — highlighted -->
            <div style="background:rgba(0,102,204,0.15); border:1px solid rgba(0,102,204,0.35); border-radius:18px; padding:28px 24px;">
                <div style="font-size:34px; margin-bottom:16px; line-height:1;">&#127963;</div>
                <h3 style="font-size:21px; font-weight:600; letter-spacing:0.231px; color:#ffffff; margin:0 0 16px;">Admin Instansi</h3>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px;">
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#6ba3e0; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Kelola laporan instansi</span>
                    </li>
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#6ba3e0; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Update status &amp; unggah foto sesudah</span>
                    </li>
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#6ba3e0; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Lihat analitik &amp; tren wilayah kerja</span>
                    </li>
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#6ba3e0; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Filter &amp; DataTable manajemen</span>
                    </li>
                </ul>
            </div>

            <!-- Super Admin -->
            <div style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:18px; padding:28px 24px;">
                <div style="font-size:34px; margin-bottom:16px; line-height:1;">&#9881;</div>
                <h3 style="font-size:21px; font-weight:600; letter-spacing:0.231px; color:#ffffff; margin:0 0 16px;">Super Admin</h3>
                <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px;">
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#0066cc; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Kelola semua pengguna &amp; peran</span>
                    </li>
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#0066cc; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Manajemen instansi &amp; kategori</span>
                    </li>
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#0066cc; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Analitik lintas semua instansi</span>
                    </li>
                    <li style="display:flex; align-items:flex-start; gap:10px;">
                        <span style="color:#0066cc; flex-shrink:0; margin-top:2px; font-size:14px;">&#10003;</span>
                        <span style="font-size:14px; letter-spacing:-0.224px; color:#cccccc;">Kontrol penuh seluruh sistem</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════
     CTA — white
     ═══════════════════════════════════════════════════════ -->
<section class="home-section" style="background:#ffffff; padding:80px 0;">
    <div class="home-inner" style="max-width:640px; margin:0 auto; padding:0 40px; text-align:center;">

        <h2 style="font-size:clamp(32px,4vw,52px); font-weight:600; line-height:1.08; letter-spacing:-0.2px; color:#1d1d1f; margin:0 0 16px;">
            Mulai membuat perubahan hari ini.
        </h2>
        <p style="font-size:17px; font-weight:400; line-height:1.47; letter-spacing:-0.374px; color:#6e6e73; margin:0 0 36px;">
            Daftar gratis, laporkan masalah di sekitar Anda, dan pantau penanganannya secara transparan bersama komunitas.
        </p>

        <div style="display:flex; gap:14px; justify-content:center; flex-wrap:wrap;">
            <a href="/register" class="btn-pill" style="font-size:17px; padding:14px 32px;">Daftar Sekarang</a>
            <a href="/dashboard" class="btn-pill-ghost" style="font-size:17px; padding:14px 32px;">Lihat Peta</a>
        </div>

        <p style="font-size:12px; letter-spacing:-0.12px; color:#7a7a7a; margin:20px 0 0;">
            Gratis untuk warga &mdash; tidak perlu kartu kredit.
        </p>

    </div>
</section>
