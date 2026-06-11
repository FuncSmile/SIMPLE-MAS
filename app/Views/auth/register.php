<div class="max-w-md mx-auto py-12 px-4">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Registrasi Akun Warga</h2>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-error">
            <ul class="list-disc pl-5">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/register" method="post" class="bg-white shadow-md rounded-lg p-6 space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="<?= old('name') ?>" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                placeholder="Masukkan nama lengkap">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="<?= old('email') ?>" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                placeholder="contoh@email.com">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
            <input type="text" name="phone" value="<?= old('phone') ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                placeholder="08xxxxxxxxxx">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
            <input type="password" name="password" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                placeholder="Minimal 6 karakter">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
            <input type="password" name="confirm_password" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                placeholder="Ulangi kata sandi">
        </div>
        <button type="submit" class="w-full bg-primary hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded-lg transition">
            Daftar
        </button>
        <p class="text-center text-sm text-gray-600">
            Sudah punya akun? <a href="/login" class="text-primary hover:underline">Login di sini</a>
        </p>
    </form>
</div>
