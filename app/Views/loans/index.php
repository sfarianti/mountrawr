<div class="mb-8">
    <h1 class="text-3xl font-black text-white tracking-tight">Peminjaman Saya</h1>
    <p class="text-slate-400 text-xs mt-1">Daftar peminjaman aktif yang sedang berjalan atau menunggu persetujuan</p>
</div>

<!-- Tabs Navigation -->
<div class="flex items-center gap-4 border-b border-slate-800 pb-4 mb-8">
    <a href="/loans" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20">
        Peminjaman Aktif
    </a>
    <a href="/loans/history" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:text-white hover:bg-slate-900 transition-all">
        Riwayat Selesai & Ditolak
    </a>
</div>

<?php if (empty($loans)): ?>
    <div class="glass-panel p-12 rounded-3xl border border-slate-800 text-center my-8">
        <div class="text-4xl mb-3">🏕️</div>
        <h3 class="text-lg font-bold text-white mb-1">Belum Ada Peminjaman Aktif</h3>
        <p class="text-slate-400 text-xs mb-6">Anda tidak memiliki pengajuan peminjaman alat yang sedang berlangsung.</p>
        <a href="/catalog" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all">
            Jelajahi Katalog Alat
        </a>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($loans as $loan): ?>
            <?php
                $statusColors = [
                    'pending' => 'bg-amber-500/20 text-amber-300 border-amber-500/40',
                    'approved' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
                    'borrowed' => 'bg-blue-500/20 text-blue-300 border-blue-500/40',
                    'returned' => 'bg-purple-500/20 text-purple-300 border-purple-500/40',
                ];
                $statusLabels = [
                    'pending' => 'Menunggu Approval Admin',
                    'approved' => 'Disetujui (Siap Diambil)',
                    'borrowed' => 'Sedang Dipinjam',
                    'returned' => 'Menunggu Konfirmasi Pengembalian',
                ];
                $color = $statusColors[$loan['status']] ?? 'bg-slate-800 text-slate-300 border-slate-700';
                $label = $statusLabels[$loan['status']] ?? $loan['status'];
                $isOverdue = ($loan['status'] === 'borrowed' && $loan['end_date'] < date('Y-m-d'));
            ?>
            <div class="glass-panel p-6 rounded-3xl border border-slate-800/80 hover:border-slate-700 transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-mono font-bold text-emerald-400 px-3 py-1 bg-slate-900 border border-slate-800 rounded-lg">
                            <?= e($loan['code']) ?>
                        </span>
                        <span class="px-3 py-1 rounded-full border text-[11px] font-bold <?= $color ?>">
                            <?= $label ?>
                        </span>
                        <?php if ($isOverdue): ?>
                            <span class="px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/40 text-[11px] font-black animate-pulse">
                                ⚠️ TERLAMBAT
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="text-xs text-slate-300 flex flex-wrap items-center gap-4 pt-1">
                        <div>
                            Mulai: <strong class="text-white"><?= date('d M Y', strtotime($loan['start_date'])) ?></strong>
                        </div>
                        <div>
                            Sampai: <strong class="text-white"><?= date('d M Y', strtotime($loan['end_date'])) ?></strong>
                        </div>
                        <div>
                            Jumlah Alat: <strong class="text-white"><?= $loan['item_count'] ?> Jenis</strong>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 pt-4 md:pt-0 border-slate-800/80">
                    <div class="text-right">
                        <span class="text-[10px] text-slate-500 block uppercase tracking-wider">Total Biaya</span>
                        <span class="text-base font-black text-emerald-400"><?= rupiah($loan['total_price']) ?></span>
                    </div>

                    <a href="/loans/<?= $loan['id'] ?>" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition-all">
                        Lihat Detail →
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
