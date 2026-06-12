</main>

<?php if (empty($GLOBALS['ci_no_footer'])): ?>
<!-- Footer: parchment canvas, dense-link typography, 64px vertical padding -->
<footer style="background:#f5f5f7; padding:64px 0 32px; border-top:1px solid #e0e0e0; margin-top:auto;">
    <div style="max-width:980px; margin:0 auto; padding:0 20px;">

        <!-- Column grid -->
        <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:48px; margin-bottom:48px;">

            <!-- Brand column -->
            <div>
                <p style="font-size:14px; font-weight:600; letter-spacing:-0.224px; line-height:1.29; color:#1d1d1f; margin:0 0 12px;">
                    SIMPEL-MAS
                </p>
                <p style="font-size:12px; line-height:1.6; color:#7a7a7a; letter-spacing:-0.12px; margin:0;">
                    Sistem Informasi Manajemen Pengaduan Lokal Masyarakat. Platform pengaduan warga yang transparan dan terpercaya untuk masyarakat Indonesia.
                </p>
            </div>

            <!-- Navigasi column -->
            <div>
                <p style="font-size:14px; font-weight:600; letter-spacing:-0.224px; line-height:1.29; color:#1d1d1f; margin:0 0 12px;">
                    Navigasi
                </p>
                <div style="display:flex; flex-direction:column;">
                    <a href="/"               style="font-size:17px; line-height:2.41; color:#0066cc; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Beranda</a>
                    <a href="/login"           style="font-size:17px; line-height:2.41; color:#0066cc; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Masuk</a>
                    <a href="/register"        style="font-size:17px; line-height:2.41; color:#0066cc; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Daftar Akun</a>
                </div>
            </div>

            <!-- Layanan column -->
            <div>
                <p style="font-size:14px; font-weight:600; letter-spacing:-0.224px; line-height:1.29; color:#1d1d1f; margin:0 0 12px;">
                    Layanan
                </p>
                <div style="display:flex; flex-direction:column;">
                    <a href="/dashboard"       style="font-size:17px; line-height:2.41; color:#0066cc; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Peta Pengaduan</a>
                    <a href="/complaint/create" style="font-size:17px; line-height:2.41; color:#0066cc; text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Buat Aduan</a>
                </div>
            </div>
        </div>

        <!-- Legal row -->
        <div style="border-top:1px solid #e0e0e0; padding-top:24px;">
            <p style="font-size:12px; font-weight:400; line-height:1.0; letter-spacing:-0.12px; color:#7a7a7a; margin:0;">
                &copy; <?= date('Y') ?> SIMPEL-MAS &mdash; Sistem Informasi Manajemen Pengaduan Lokal Masyarakat.
            </p>
        </div>
    </div>
</footer>

<style>
@media (max-width: 640px) {
    footer > div > div:first-child {
        grid-template-columns: 1fr !important;
        gap: 32px !important;
    }
}
</style>
<?php endif; ?>

</body>
</html>
