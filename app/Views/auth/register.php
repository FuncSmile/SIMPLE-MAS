<!-- ── REGISTER: centered on parchment canvas ── -->
<div style="background:#f5f5f7; min-height:calc(100vh - 44px); display:flex; align-items:center; justify-content:center; padding:48px 20px;">
    <div style="width:100%; max-width:400px;">

        <h1 style="font-size:34px; font-weight:600; line-height:1.47; letter-spacing:-0.374px; color:#1d1d1f; text-align:center; margin:0 0 8px;">
            Daftar Akun
        </h1>
        <p style="font-size:17px; font-weight:400; line-height:1.47; letter-spacing:-0.374px; color:#7a7a7a; text-align:center; margin:0 0 32px;">
            Bergabung sebagai warga SIMPEL-MAS
        </p>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error" style="margin-bottom:20px;">
                <ul style="margin:0; padding-left:20px;">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/register" method="post" class="card-utility" style="display:flex; flex-direction:column; gap:16px;">
            <?= csrf_field() ?>

            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">
                    Nama Lengkap
                </label>
                <input type="text" name="name" value="<?= old('name') ?>" required
                       class="input-field" placeholder="Masukkan nama lengkap">
            </div>

            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">
                    Email
                </label>
                <input type="email" name="email" value="<?= old('email') ?>" required
                       class="input-field" placeholder="contoh@email.com">
            </div>

            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">
                    Nomor Telepon
                </label>
                <input type="text" name="phone" value="<?= old('phone') ?>"
                       class="input-field" placeholder="08xxxxxxxxxx">
            </div>

            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">
                    Kata Sandi
                </label>
                <input type="password" name="password" required
                       class="input-field" placeholder="Minimal 6 karakter">
            </div>

            <div>
                <label style="display:block; font-size:14px; font-weight:600; letter-spacing:-0.224px; color:#1d1d1f; margin-bottom:6px;">
                    Konfirmasi Kata Sandi
                </label>
                <input type="password" name="confirm_password" required
                       class="input-field" placeholder="Ulangi kata sandi">
            </div>

            <button type="submit" class="btn-pill" style="width:100%; text-align:center; margin-top:8px;">
                Daftar
            </button>

            <p style="font-size:14px; font-weight:400; line-height:1.43; letter-spacing:-0.224px; color:#7a7a7a; text-align:center; margin:0;">
                Sudah punya akun?
                <a href="/login" style="color:#0066cc; text-decoration:none;"
                   onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                    Masuk di sini
                </a>
            </p>
        </form>
    </div>
</div>
