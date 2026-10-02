<div class="mb-8">
    <h1 class="text-3xl font-black text-white tracking-tight">Riwayat Peminjaman</h1>
    <p class="text-slate-400 text-xs mt-1">Daftar peminjaman yang telah selesai, ditolak, atau dibatalkan</p>
</div>

<!-- Tabs Navigation -->
<div class="flex items-center gap-4 border-b border-slate-800 pb-4 mb-8">
    <a href="/loans" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:text-white hover:bg-slate-900 transition-all">
        Peminjaman Aktif
    </a>
    <a href="/loans/history" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20">
        Riwayat Selesai & Ditolak
    </a>
</div>

<?php if (empty($loans)): ?>
    <div class="glass-panel p-12 rounded-3xl border border-slate-800 text-center my-8">
        <div class="text-4xl mb-3">📜</div>
        <h3 class="text-lg font-bold text-white mb-1">Riwayat Masih Kosong</h3>
        <p class="text-slate-400 text-xs">Belum ada riwayat transaksi peminjaman yang selesai atau dibatalkan.</p>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($loans as $loan): ?>
            <?php
                $statusColors = [
                    'completed' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
                    'rejected' => 'bg-rose-500/20 text-rose-300 border-rose-500/40',
                    'cancelled' => 'bg-slate-800 text-slate-400 border-slate-700',
                ];
                $statusLabels = [
                    'completed' => 'Selesai',
                    'rejected' => 'Ditolak',
                    'cancelled' => 'Dibatalkan',
                ];
                $color = $statusColors[$loan['status']] ?? 'bg-slate-800 text-slate-300 border-slate-700';
                $label = $statusLabels[$loan['status']] ?? $loan['status'];
            ?>
            <div class="glass-panel p-6 rounded-3xl border border-slate-800/80 hover:border-slate-700 transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-mono font-bold text-slate-400 px-3 py-1 bg-slate-900 border border-slate-800 rounded-lg">
                            <?= e($loan['code']) ?>
                        </span>
                        <span class="px-3 py-1 rounded-full border text-[11px] font-bold <?= $color ?>">
                            <?= $label ?>
                        </span>
                    </div>

                    <div class="text-xs text-slate-400 flex flex-wrap items-center gap-4 pt-1">
                        <div>
                            Periode: <span class="text-slate-200"><?= date('d M Y', strtotime($loan['start_date'])) ?> — <?= date('d M Y', strtotime($loan['end_date'])) ?></span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 pt-4 md:pt-0 border-slate-800/80">
                    <div class="text-right">
                        <span class="text-[10px] text-slate-500 block uppercase tracking-wider">Total Biaya</span>
                        <span class="text-base font-bold text-white"><?= rupiah($loan['total_price']) ?></span>
                    </div>

                    <a href="/loans/<?= $loan['id'] ?>" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition-all">
                        Lihat Detail →
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
