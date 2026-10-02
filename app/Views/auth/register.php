<div class="max-w-md mx-auto my-6">
    <div class="glass-panel p-8 rounded-3xl border border-slate-800 shadow-2xl shadow-emerald-950/20">
        <div class="text-center mb-8">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 items-center justify-center font-black text-slate-950 text-2xl mb-3 shadow-lg shadow-emerald-500/20">
                ▲
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Buat Akun Mountrawr</h1>
            <p class="text-slate-400 text-xs mt-1">Daftar akun peminjam untuk mulai menyewa perlengkapan gunung</p>
        </div>

        <form action="/register" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="<?= e(old('name')) ?>" required autofocus 
                       placeholder="Budi Pendaki"
                       class="w-full px-4 py-3 bg-slate-900/80 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Alamat Email</label>
                <input type="email" id="email" name="email" value="<?= e(old('email')) ?>" required 
                       placeholder="nama@email.com"
                       class="w-full px-4 py-3 bg-slate-900/80 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Nomor WhatsApp / HP</label>
                <input type="text" id="phone" name="phone" value="<?= e(old('phone')) ?>" 
                       placeholder="081234567890"
                       class="w-full px-4 py-3 bg-slate-900/80 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Password (Minimal 8 Karakter)</label>
                <input type="password" id="password" name="password" required 
                       placeholder="••••••••"
                       class="w-full px-4 py-3 bg-slate-900/80 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required 
                       placeholder="••••••••"
                       class="w-full px-4 py-3 bg-slate-900/80 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
            </div>

            <button type="submit" class="w-full py-3.5 px-4 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-sm rounded-xl shadow-lg shadow-emerald-500/25 transition-all transform hover:-translate-y-0.5 mt-2">
                Daftar Akun Sekarang
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-800/80 text-center text-xs text-slate-400">
            Sudah punya akun? 
            <a href="/login" class="text-emerald-400 hover:text-emerald-300 font-bold transition-colors">Masuk di sini</a>
        </div>
    </div>
</div>
