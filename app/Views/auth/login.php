<div class="max-w-md mx-auto py-12 px-4">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login</h2>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-error">
            <ul class="list-disc pl-5">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/login" method="post" class="bg-white shadow-md rounded-lg p-6 space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="<?= old('email') ?>" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                placeholder="contoh@email.com">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
            <input type="password" name="password" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                placeholder="Masukkan kata sandi">
        </div>
        <button type="submit" class="w-full bg-primary hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded-lg transition">
            Masuk
        </button>
        <p class="text-center text-sm text-gray-600">
            Belum punya akun? <a href="/register" class="text-primary hover:underline">Daftar di sini</a>
        </p>
    </form>
</div>
