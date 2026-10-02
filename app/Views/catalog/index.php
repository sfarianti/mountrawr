<div class="mb-8">
    <h1 class="text-3xl font-black text-white tracking-tight">Katalog Peralatan Pendakian</h1>
    <p class="text-slate-400 text-xs mt-1">Cari dan pilih peralatan outdoor berkualitas untuk petualangan Anda</p>
</div>

<!-- Filters Bar -->
<div class="glass-panel p-6 rounded-3xl border border-slate-800/80 mb-8">
    <form action="/catalog" method="GET" class="flex flex-col md:flex-row gap-4">
        <!-- Search Input -->
        <div class="flex-grow">
            <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari nama alat (mis. Tenda Eiger, Carrier Osprey...)" 
                   class="w-full px-4 py-3 bg-slate-900/80 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
        </div>

        <!-- Category Dropdown -->
        <div class="w-full md:w-64">
            <select name="category" onchange="this.form.submit()" 
                    class="w-full px-4 py-3 bg-slate-900/80 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500 transition-all">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat['slug']) ?>" <?= ($selectedCategory && $selectedCategory['slug'] === $cat['slug']) ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
            Cari Alat
        </button>

        <?php if ($search || $selectedCategory): ?>
            <a href="/catalog" class="px-4 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-sm rounded-xl transition-all text-center">
                Reset
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Items Grid -->
<?php if (empty($items)): ?>
    <div class="glass-panel p-12 rounded-3xl border border-slate-800 text-center my-12">
        <div class="text-4xl mb-3">🔍</div>
        <h3 class="text-lg font-bold text-white mb-1">Alat Tidak Ditemukan</h3>
        <p class="text-slate-400 text-xs">Coba cari dengan kata kunci lain atau pilih kategori yang berbeda.</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($items as $item): ?>
            <div class="glass-panel rounded-3xl border border-slate-800/80 overflow-hidden hover:border-slate-700 transition-all group flex flex-col">
                <div class="h-48 bg-slate-900 relative flex items-center justify-center overflow-hidden">
                    <?php if (!empty($item['image_path'])): ?>
                        <img src="/<?= e($item['image_path']) ?>" alt="<?= e($item['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                        <div class="text-4xl text-slate-700 font-bold group-hover:scale-110 transition-transform">🎒</div>
                    <?php endif; ?>
                    
                    <div class="absolute top-3 left-3 px-3 py-1 rounded-full bg-slate-950/80 backdrop-blur border border-slate-800 text-[11px] font-bold text-emerald-400">
                        <?= e($item['category_name']) ?>
                    </div>

                    <div class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $item['condition_status'] === 'baik' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' ?>">
                        Kondisi: <?= e(ucwords(str_replace('_', ' ', $item['condition_status']))) ?>
                    </div>
                </div>

                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="font-extrabold text-lg text-white mb-2 group-hover:text-emerald-400 transition-colors">
                        <?= e($item['name']) ?>
                    </h3>
                    <p class="text-slate-400 text-xs line-clamp-2 mb-4 leading-relaxed">
                        <?= e($item['description']) ?>
                    </p>

                    <div class="flex items-center gap-4 text-xs text-slate-400 mb-6">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Stok Fisik: <strong class="text-white"><?= (int)$item['stock_total'] ?> Unit</strong>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-slate-800/80 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider block">Harga Sewa / Hari</span>
                            <span class="text-base font-black text-emerald-400"><?= rupiah($item['price_per_day']) ?></span>
                        </div>
                        <a href="/catalog/<?= e($item['slug']) ?>" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs rounded-xl shadow-md transition-all">
                            Lihat & Pinjam
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="flex justify-center items-center gap-2 mt-12">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <a href="/catalog?page=<?= $p ?>&category=<?= e($selectedCategory['slug'] ?? '') ?>&q=<?= urlencode($search) ?>" 
                   class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-xs transition-all <?= $p === $currentPage ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20' : 'bg-slate-900 border border-slate-800 text-slate-400 hover:text-white' ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
