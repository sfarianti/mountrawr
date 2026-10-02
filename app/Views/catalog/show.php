<div class="mb-6">
    <a href="/catalog" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-emerald-400 transition-colors">
        ← Kembali ke Katalog Alat
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Item Image & Info -->
    <div class="lg:col-span-7 space-y-6">
        <div class="glass-panel rounded-3xl border border-slate-800/80 p-6 overflow-hidden">
            <div class="h-80 bg-slate-900 rounded-2xl flex items-center justify-center overflow-hidden mb-6 relative">
                <?php if (!empty($item['image_path'])): ?>
                    <img src="/<?= e($item['image_path']) ?>" alt="<?= e($item['name']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="text-6xl text-slate-700">🏕️</div>
                <?php endif; ?>

                <div class="absolute top-4 left-4 px-3 py-1 rounded-full bg-slate-950/80 backdrop-blur border border-slate-800 text-xs font-bold text-emerald-400">
                    <?= e($item['category_name']) ?>
                </div>
            </div>

            <h1 class="text-3xl font-black text-white tracking-tight mb-3"><?= e($item['name']) ?></h1>

            <div class="flex items-center gap-4 text-xs mb-6">
                <span class="px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-slate-300 font-semibold">
                    Stok Total: <?= (int)$item['stock_total'] ?> Unit
                </span>
                <span class="px-3 py-1 rounded-full <?= $item['condition_status'] === 'baik' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' ?> font-semibold">
                    Kondisi: <?= e(ucwords(str_replace('_', ' ', $item['condition_status']))) ?>
                </span>
            </div>

            <div class="border-t border-slate-800/80 pt-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Alat</h3>
                <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line"><?= e($item['description']) ?></p>
            </div>
        </div>

        <!-- 30-Day Availability Calendar Matrix -->
        <div class="glass-panel rounded-3xl border border-slate-800/80 p-6">
            <h3 class="text-sm font-bold text-white mb-2 flex items-center gap-2">
                <span>📅 Kalender Ketersediaan (30 Hari Ke Depan)</span>
            </h3>
            <p class="text-slate-400 text-xs mb-4">Warna hijau menandakan unit alat masih tersedia untuk dipinjam pada tanggal tersebut.</p>

            <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-10 gap-2">
                <?php foreach ($calendar as $cal): ?>
                    <div class="p-2 rounded-xl border text-center transition-all <?= $cal['is_available'] ? 'bg-emerald-950/30 border-emerald-500/30 text-emerald-300' : 'bg-rose-950/30 border-rose-500/30 text-rose-300 opacity-60' ?>">
                        <div class="text-[10px] uppercase font-bold text-slate-400"><?= $cal['day_name'] ?></div>
                        <div class="text-sm font-black"><?= $cal['day'] ?> <?= $cal['month'] ?></div>
                        <div class="text-[10px] font-semibold mt-1">
                            <?= $cal['available'] ?> sisa
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Loan Application Form Sidebar -->
    <div class="lg:col-span-5">
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800/80 sticky top-28 glow-emerald">
            <h2 class="text-xl font-extrabold text-white mb-1">Ajukan Peminjaman</h2>
            <p class="text-slate-400 text-xs mb-6">Pilih tanggal mulai & tanggal pengembalian alat</p>

            <!-- Date Availability Checker Form -->
            <form action="/catalog/<?= e($item['slug']) ?>" method="GET" class="space-y-4 mb-6 pb-6 border-b border-slate-800/80">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="<?= e($startDate) ?>" min="<?= date('Y-m-d') ?>" 
                               class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500"
                               onchange="this.form.submit()">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal Kembali</label>
                        <input type="date" name="end_date" value="<?= e($endDate) ?>" min="<?= e($startDate) ?>" 
                               class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500"
                               onchange="this.form.submit()">
                    </div>
                </div>
            </form>

            <!-- Stock Availability Badge -->
            <div class="p-4 rounded-2xl mb-6 <?= $availableStock > 0 ? 'bg-emerald-950/60 border border-emerald-500/40 text-emerald-300' : 'bg-rose-950/60 border border-rose-500/40 text-rose-300' ?>">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold">Ketersediaan Tanggal Ini:</span>
                    <span class="text-sm font-black"><?= $availableStock ?> Unit Tersedia</span>
                </div>
                <div class="text-[11px] mt-1 text-slate-400">
                    Durasi sewa: <strong class="text-white"><?= $days ?> Hari</strong>
                </div>
            </div>

            <!-- Submit Form -->
            <?php if (auth()): ?>
                <?php if ($availableStock > 0): ?>
                    <form action="/loans" method="POST" class="space-y-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="start_date" value="<?= e($startDate) ?>">
                        <input type="hidden" name="end_date" value="<?= e($endDate) ?>">
                        <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Jumlah Unit (Qty)</label>
                            <input type="number" name="quantity" value="1" min="1" max="<?= $availableStock ?>" required
                                   class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea name="note" rows="2" placeholder="Misal: Mohon disiapkan sebelum jam 08.00 pagi..." 
                                      class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500"></textarea>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800/80 space-y-2">
                            <div class="flex justify-between text-xs text-slate-400">
                                <span>Harga per Hari</span>
                                <span><?= rupiah($item['price_per_day']) ?></span>
                            </div>
                            <div class="flex justify-between text-xs text-slate-400">
                                <span>Durasi Peminjaman</span>
                                <span><?= $days ?> Hari</span>
                            </div>
                            <div class="border-t border-slate-800 pt-2 flex justify-between text-sm font-extrabold text-white">
                                <span>Estimasi Total</span>
                                <span class="text-emerald-400 font-black"><?= rupiah($estimatedPrice) ?></span>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-sm rounded-xl shadow-lg shadow-emerald-500/25 transition-all transform hover:-translate-y-0.5">
                            Kirim Pengajuan Peminjaman
                        </button>
                    </form>
                <?php else: ?>
                    <button disabled class="w-full py-4 bg-slate-800 text-slate-500 font-bold text-sm rounded-xl border border-slate-700 cursor-not-allowed">
                        Stok Penuh Pada Tanggal Ini
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center p-4 bg-slate-900 border border-slate-800 rounded-2xl">
                    <p class="text-xs text-slate-300 mb-3">Silakan masuk ke akun Anda terlebih dahulu untuk mengajukan peminjaman.</p>
                    <a href="/login" class="inline-block px-6 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs rounded-xl shadow-md transition-all">
                        Login Ke Akun
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
