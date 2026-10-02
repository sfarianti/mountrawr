<!-- Hero Section -->
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-slate-900 via-slate-950 to-slate-950 border border-slate-800/80 p-8 sm:p-14 my-4 glow-emerald">
    <div class="absolute -right-20 -top-20 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-3xl relative z-10">
        <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight leading-none mb-6">
            Sewa Peralatan Pendakian <span class="bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">Standar Ekspedisi</span>
        </h1>
        <p class="text-slate-300 text-base sm:text-lg mb-8 leading-relaxed">
            Nikmati petualangan mendaki gunung dengan peralatan berkualitas tinggi, bersih, dan siap tempur. Proses pengajuan instan, transparan, dan terpercaya.
        </p>
        <div class="flex flex-wrap gap-4">
            <a href="/catalog" class="px-8 py-4 bg-emerald-400 hover:bg-emerald-300 text-slate-950 font-extrabold text-sm rounded-2xl shadow-xl shadow-emerald-400/20 hover:shadow-emerald-400/30 transition-all transform hover:-translate-y-0.5">
                Jelajahi Katalog Alat
            </a>
            <?php if (!auth()): ?>
                <a href="/register" class="px-8 py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-2xl border border-slate-700 transition-all">
                    Daftar Akun Peminjam
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Categories Grid -->
<div class="my-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Kategori Peralatan</h2>
            <p class="text-slate-400 text-xs mt-1">Pilih kategori alat yang Anda butuhkan untuk pendakian</p>
        </div>
        <a href="/catalog" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center gap-1">
            Lihat Semua →
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <?php foreach ($categories as $cat): ?>
            <a href="/catalog?category=<?= e($cat['slug']) ?>" 
               class="glass-panel p-5 rounded-2xl border border-slate-800/80 hover:border-emerald-500/50 hover:bg-slate-900/60 transition-all text-center group">
                <div class="w-12 h-12 rounded-xl bg-slate-800 group-hover:bg-emerald-500/20 text-emerald-400 mx-auto flex items-center justify-center text-2xl font-bold mb-3 transition-colors">
                    🏕️
                </div>
                <h3 class="font-bold text-sm text-white group-hover:text-emerald-400 transition-colors"><?= e($cat['name']) ?></h3>
                <p class="text-[11px] text-slate-500 mt-1 line-clamp-1"><?= e($cat['description']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Featured Items Grid -->
<div class="my-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Alat Siap Pinjam</h2>
            <p class="text-slate-400 text-xs mt-1">Peralatan favorit siap pakai untuk petualangan Anda</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($featuredItems as $item): ?>
            <div class="glass-panel rounded-3xl border border-slate-800/80 overflow-hidden hover:border-slate-700 transition-all group flex flex-col">
                <div class="h-48 bg-slate-900 relative flex items-center justify-center overflow-hidden">
                    <?php if ($item['image_path']): ?>
                        <img src="/<?= e($item['image_path']) ?>" alt="<?= e($item['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                        <div class="text-4xl text-slate-700 font-bold group-hover:scale-110 transition-transform">🏔️</div>
                    <?php endif; ?>
                    <div class="absolute top-3 right-3 px-3 py-1 rounded-full bg-slate-950/80 backdrop-blur border border-slate-800 text-[11px] font-bold text-emerald-400">
                        <?= e($item['category_name']) ?>
                    </div>
                </div>
                
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="font-extrabold text-lg text-white mb-2 group-hover:text-emerald-400 transition-colors">
                        <?= e($item['name']) ?>
                    </h3>
                    <p class="text-slate-400 text-xs line-clamp-2 mb-4 leading-relaxed">
                        <?= e($item['description']) ?>
                    </p>

                    <div class="mt-auto pt-4 border-t border-slate-800/80 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider block">Sewa Per Hari</span>
                            <span class="text-base font-black text-emerald-400"><?= rupiah($item['price_per_day']) ?></span>
                        </div>
                        <a href="/catalog/<?= e($item['slug']) ?>" class="px-4 py-2 bg-slate-800 hover:bg-emerald-500 hover:text-slate-950 text-white font-bold text-xs rounded-xl transition-all">
                            Detail & Cek Stok
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
