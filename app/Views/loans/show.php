<div class="mb-6">
    <a href="/loans" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-emerald-400 transition-colors">
        ← Kembali ke Daftar Peminjaman
    </a>
</div>

<?php
    $statusColors = [
        'pending' => 'bg-amber-500/20 text-amber-300 border-amber-500/40',
        'approved' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
        'borrowed' => 'bg-blue-500/20 text-blue-300 border-blue-500/40',
        'returned' => 'bg-purple-500/20 text-purple-300 border-purple-500/40',
        'completed' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
        'rejected' => 'bg-rose-500/20 text-rose-300 border-rose-500/40',
        'cancelled' => 'bg-slate-800 text-slate-400 border-slate-700',
    ];
    $statusLabels = [
        'pending' => 'Menunggu Approval Admin',
        'approved' => 'Disetujui (Siap Diambil)',
        'borrowed' => 'Sedang Dipinjam',
        'returned' => 'Menunggu Konfirmasi Pengembalian',
        'completed' => 'Selesai',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];
    $color = $statusColors[$loan['status']] ?? 'bg-slate-800 text-slate-300 border-slate-700';
    $label = $statusLabels[$loan['status']] ?? $loan['status'];
?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Main Details Column -->
    <div class="lg:col-span-8 space-y-6">
        <!-- Header Panel -->
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800/80">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <span class="text-xs font-mono font-bold text-emerald-400 block mb-1">KODE PERMINTAAN</span>
                    <h1 class="text-2xl font-black text-white font-mono tracking-tight"><?= e($loan['code']) ?></h1>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-4 py-1.5 rounded-full border text-xs font-extrabold <?= $color ?>">
                        <?= $label ?>
                    </span>
                    <?php if (!empty($loan['is_overdue'])): ?>
                        <span class="px-3 py-1.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/40 text-xs font-black animate-pulse">
                            ⚠️ TERLAMBAT
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-900/90 border border-slate-800/80 text-xs">
                <div>
                    <span class="text-slate-500 block">Tanggal Mulai</span>
                    <strong class="text-white font-bold"><?= date('d M Y', strtotime($loan['start_date'])) ?></strong>
                </div>
                <div>
                    <span class="text-slate-500 block">Tanggal Pengembalian</span>
                    <strong class="text-white font-bold"><?= date('d M Y', strtotime($loan['end_date'])) ?></strong>
                </div>
                <div>
                    <span class="text-slate-500 block">Durasi</span>
                    <strong class="text-white font-bold"><?= $loan['duration_days'] ?> Hari</strong>
                </div>
                <div>
                    <span class="text-slate-500 block">Total Biaya</span>
                    <strong class="text-emerald-400 font-extrabold text-sm"><?= rupiah($loan['total_price']) ?></strong>
                </div>
            </div>

            <?php if (!empty($loan['note'])): ?>
                <div class="mt-4 p-4 rounded-2xl bg-slate-900/50 border border-slate-800 text-xs text-slate-300">
                    <strong class="text-slate-400 block mb-1">Catatan Peminjam:</strong>
                    <?= e($loan['note']) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($loan['rejection_reason'])): ?>
                <div class="mt-4 p-4 rounded-2xl bg-rose-950/40 border border-rose-500/30 text-xs text-rose-300">
                    <strong class="text-rose-400 block mb-1">Alasan Penolakan Admin:</strong>
                    <?= e($loan['rejection_reason']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Borrowed Items List -->
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800/80">
            <h3 class="text-lg font-bold text-white mb-4">Daftar Barang yang Dipinjam</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/80 uppercase text-slate-400 font-bold border-b border-slate-800">
                        <tr>
                            <th class="p-3">Nama Alat</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3 text-center">Qty</th>
                            <th class="p-3 text-right">Harga / Hari</th>
                            <th class="p-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <?php foreach ($loan['items'] as $item): ?>
                            <tr>
                                <td class="p-3 font-semibold text-white">
                                    <a href="/catalog/<?= e($item['item_slug']) ?>" class="hover:text-emerald-400 transition-colors">
                                        <?= e($item['item_name']) ?>
                                    </a>
                                </td>
                                <td class="p-3 text-slate-400"><?= e($item['category_name']) ?></td>
                                <td class="p-3 text-center font-bold text-white"><?= (int)$item['quantity'] ?></td>
                                <td class="p-3 text-right"><?= rupiah($item['price_per_day']) ?></td>
                                <td class="p-3 text-right font-bold text-emerald-400"><?= rupiah($item['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Return Details Panel (if exists) -->
        <?php if (!empty($loan['return'])): ?>
            <?php $ret = $loan['return']; ?>
            <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800/80">
                <h3 class="text-lg font-bold text-white mb-4">Informasi Pengembalian Barang</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
                        <span class="text-slate-500 block mb-1">Tanggal Dikembalikan</span>
                        <strong class="text-white"><?= date('d M Y H:i', strtotime($ret['returned_at'])) ?></strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
                        <span class="text-slate-500 block mb-1">Kondisi Fisik Alat</span>
                        <strong class="text-emerald-400 capitalize"><?= e(str_replace('_', ' ', $ret['item_condition'])) ?></strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
                        <span class="text-slate-500 block mb-1">Keterlambatan & Denda</span>
                        <strong class="text-rose-400"><?= (int)$ret['late_days'] ?> Hari (<?= rupiah($ret['penalty_amount']) ?>)</strong>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar Column: User Actions & Audit Timeline -->
    <div class="lg:col-span-4 space-y-6">
        <!-- User Actions Panel -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800/80">
            <h3 class="text-sm font-bold text-white mb-4">Tindakan</h3>

            <?php if (in_array($loan['status'], ['pending', 'approved'])): ?>
                <form action="/loans/<?= $loan['id'] ?>/cancel" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="w-full py-3 bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 font-bold text-xs rounded-xl border border-rose-500/30 transition-all">
                        Batalkan Peminjaman
                    </button>
                </form>
            <?php elseif ($loan['status'] === 'borrowed'): ?>
                <form action="/loans/<?= $loan['id'] ?>/return" method="POST" class="space-y-3">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Catatan Pengembalian (Opsional)</label>
                        <textarea name="note" rows="2" placeholder="Catatan kondisi saat pengembalian..." 
                                  class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500"></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
                        Laporkan Pengembalian Alat
                    </button>
                </form>
            <?php else: ?>
                <p class="text-slate-500 text-xs">Tidak ada tindakan lebih lanjut yang dapat dilakukan untuk status ini.</p>
            <?php endif; ?>
        </div>

        <!-- Status Log Timeline -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800/80">
            <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <span>⏱️ Timeline Perubahan Status</span>
            </h3>

            <div class="relative border-l-2 border-slate-800 ml-3 space-y-6">
                <?php foreach ($loan['logs'] as $log): ?>
                    <div class="relative pl-6">
                        <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-emerald-500 border-4 border-slate-950"></div>
                        <div class="text-xs font-bold text-white capitalize">
                            Status: <span class="text-emerald-400"><?= e($log['to_status']) ?></span>
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5">
                            <?= date('d M Y H:i:s', strtotime($log['created_at'])) ?>
                            <?php if (!empty($log['changed_by_name'])): ?>
                                • Oleh <?= e($log['changed_by_name']) ?>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($log['note'])): ?>
                            <div class="text-[11px] text-slate-400 mt-1 italic">
                                "<?= e($log['note']) ?>"
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
