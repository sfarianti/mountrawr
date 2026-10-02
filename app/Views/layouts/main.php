<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Mountrawr — Outdoor Gear Rental') ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        emerald: {
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glow-emerald {
            box-shadow: 0 0 25px -5px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body class="flex flex-col min-h-full bg-slate-950 text-slate-100 antialiased selection:bg-emerald-500 selection:text-slate-950">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 glass-panel border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <div class="flex items-center gap-3">
                    <a href="/" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center font-black text-slate-950 text-xl shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                            ▲
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-extrabold tracking-tight text-white group-hover:text-emerald-400 transition-colors">
                                MOUNTRAWR
                            </span>
                            <span class="text-[10px] font-semibold text-emerald-400/80 uppercase tracking-widest -mt-1">
                                Premium Gear Rental
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold">
                    <a href="/" class="hover:text-emerald-400 transition-colors">Beranda</a>
                    <a href="/catalog" class="hover:text-emerald-400 transition-colors">Katalog Alat</a>
                    <?php if (auth()): ?>
                        <?php if (\App\Core\Auth::isAdmin()): ?>
                            <a href="/admin/loans" class="text-emerald-400 hover:text-emerald-300 font-bold flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Kelola Peminjaman
                            </a>
                            <a href="/admin/items" class="hover:text-emerald-400 transition-colors">Kelola Alat</a>
                            <a href="/admin/categories" class="hover:text-emerald-400 transition-colors">Kategori</a>
                        <?php else: ?>
                            <a href="/loans" class="hover:text-emerald-400 transition-colors">Peminjaman Saya</a>
                            <a href="/loans/history" class="hover:text-emerald-400 transition-colors">Riwayat</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-4">
                    <?php if (auth()): ?>
                        <div class="hidden sm:flex flex-col text-right mr-2">
                            <span class="text-sm font-bold text-white"><?= e(auth()['name']) ?></span>
                            <span class="text-xs text-slate-400 uppercase tracking-wider font-medium"><?= e(auth()['role']) ?></span>
                        </div>
                        <form action="/logout" method="POST" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="px-4 py-2 text-xs font-bold text-slate-300 hover:text-white bg-slate-800/80 hover:bg-slate-700 border border-slate-700 rounded-xl transition-all">
                                Keluar
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="/login" class="px-4 py-2 text-xs font-bold text-slate-300 hover:text-white transition-colors">
                            Masuk
                        </a>
                        <a href="/register" class="px-5 py-2.5 text-xs font-bold text-slate-950 bg-emerald-400 hover:bg-emerald-300 rounded-xl shadow-lg shadow-emerald-400/20 hover:shadow-emerald-400/30 transition-all transform hover:-translate-y-0.5">
                            Daftar Akun
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-6">
        <?php if ($msg = flash('success')): ?>
            <div class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-300 flex items-center justify-between gap-3 shadow-lg shadow-emerald-950/50">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <div class="text-sm font-medium"><?= $msg ?></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($msg = flash('error')): ?>
            <div class="p-4 rounded-2xl bg-rose-950/80 border border-rose-500/40 text-rose-300 flex items-center justify-between gap-3 shadow-lg shadow-rose-950/50">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-sm font-medium"><?= $msg ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-8">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-900 bg-slate-950/90 py-10 text-slate-400 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-500 text-slate-950 flex items-center justify-center font-black">▲</div>
                <span class="font-bold text-sm text-slate-200">MOUNTRAWR</span>
                <span class="text-slate-600">|</span>
                <span>Platform Persewaan Peralatan Outdoor & Pendakian Gunung</span>
            </div>
            <div class="text-slate-500 text-center md:text-right">
                &copy; <?= date('Y') ?> Mountrawr Outdoor Services. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
