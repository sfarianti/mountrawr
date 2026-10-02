<div class="mb-6">
    <a href="/admin/loans" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-emerald-400 transition-colors">
        ← Kembali ke Dashboard Peminjaman Admin
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
    $color = $statusColors[$loan['status']] ?? 'bg-slate-800 text-slate-300';
?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Main Details Column -->
    <div class="lg:col-span-8 space-y-6">
        <!-- Header Panel -->
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800/80">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <span class="text-xs font-mono font-bold text-emerald-400 block mb-1">KODE KONTRAK</span>
                    <h1 class="text-2xl font-black text-white font-mono tracking-tight"><?= e($loan['code']) ?></h1>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-4 py-1.5 rounded-full border text-xs font-extrabold uppercase <?= $color ?>">
                        <?= e($loan['status']) ?>
                    </span>
                    <?php if (!empty($loan['is_overdue'])): ?>
                        <span class="px-3 py-1.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/40 text-xs font-black animate-pulse">
                            ⚠️ TERLAMBAT
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Borrower Info -->
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 mb-6 space-y-1 text-xs">
                <div class="font-bold text-slate-400 uppercase tracking-wider mb-2">Informasi Peminjam</div>
                <div class="text-sm font-bold text-white"><?= e($loan['user_name']) ?></div>
                <div class="text-slate-400">Email: <?= e($loan['user_email']) ?> | WhatsApp: <?= e($loan['user_phone'] ?: '-') ?></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-900/90 border border-slate-800/80 text-xs">
                <div>
                    <span class="text-slate-500 block">Tanggal Mulai</span>
                    <strong class="text-white font-bold"><?= date('d M Y', strtotime($loan['start_date'])) ?></strong>
                </div>
                <div>
                    <span class="text-slate-500 block">Tanggal Kembali</span>
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
        </div>

        <!-- Borrowed Items List -->
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800/80">
            <h3 class="text-lg font-bold text-white mb-4">Daftar Peralatan yang Membutuhkan Persetujuan</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/80 uppercase text-slate-400 font-bold border-b border-slate-800">
                        <tr>
                            <th class="p-3">Nama Alat</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3 text-center">Qty Dipinjam</th>
                            <th class="p-3 text-right">Harga / Hari</th>
                            <th class="p-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <?php foreach ($loan['items'] as $item): ?>
                            <tr>
                                <td class="p-3 font-semibold text-white"><?= e($item['item_name']) ?></td>
                                <td class="p-3 text-slate-400"><?= e($item['category_name']) ?></td>
                                <td class="p-3 text-center font-bold text-white"><?= (int)$item['quantity'] ?> Unit</td>
                                <td class="p-3 text-right"><?= rupiah($item['price_per_day']) ?></td>
                                <td class="p-3 text-right font-bold text-emerald-400"><?= rupiah($item['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Return Report if exists -->
        <?php if (!empty($loan['return'])): ?>
            <?php $ret = $loan['return']; ?>
            <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800/80">
                <h3 class="text-lg font-bold text-white mb-4">Laporan Pengembalian Barang</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
                        <span class="text-slate-500 block mb-1">Tanggal Laporan</span>
                        <strong class="text-white"><?= date('d M Y H:i', strtotime($ret['returned_at'])) ?></strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
                        <span class="text-slate-500 block mb-1">Kondisi Alat Catatan</span>
                        <strong class="text-emerald-400 capitalize"><?= e(str_replace('_', ' ', $ret['item_condition'])) ?></strong>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
                        <span class="text-slate-500 block mb-1">Status Konfirmasi</span>
                        <strong class="text-slate-200"><?= $ret['confirmed_by'] ? 'Dikonfirmasi oleh ' . e($ret['confirmed_by_name']) : 'Menunggu Konfirmasi' ?></strong>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Admin Action Sidebar & Timeline -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Admin Action Controls -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800/80 glow-emerald">
            <h3 class="text-sm font-bold text-white mb-4">Panel Aksi Admin</h3>

            <?php if ($loan['status'] === 'pending'): ?>
                <!-- Approve Form -->
                <form action="/admin/loans/<?= $loan['id'] ?>/approve" method="POST" class="mb-4">
                    <?= csrf_field() ?>
                    <button type="submit" class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
                        ✓ Setujui Peminjaman (Approve)
                    </button>
                </form>

                <!-- Reject Form -->
                <form action="/admin/loans/<?= $loan['id'] ?>/reject" method="POST" class="space-y-3 pt-4 border-t border-slate-800">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-[11px] font-semibold text-rose-300 mb-1">Alasan Penolakan (Wajib)</label>
                        <input type="text" name="rejection_reason" required placeholder="Misal: Stok fisik rusak / tidak siap..." 
                               class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
                    </div>
                    <button type="submit" class="w-full py-3 bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 font-bold text-xs rounded-xl border border-rose-500/30 transition-all">
                        ✕ Tolak Peminjaman
                    </button>
                </form>
            <?php elseif ($loan['status'] === 'approved'): ?>
                <form action="/admin/loans/<?= $loan['id'] ?>/borrowed" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="w-full py-3.5 bg-blue-500 hover:bg-blue-400 text-white font-black text-xs rounded-xl shadow-lg shadow-blue-500/20 transition-all">
                        📦 Tandai Alat Telah Diambil (Borrowed)
                    </button>
                </form>
            <?php elseif (in_array($loan['status'], ['borrowed', 'returned'])): ?>
                <!-- Confirm Return Form -->
                <form action="/admin/loans/<?= $loan['id'] ?>/confirm-return" method="POST" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Kondisi Alat Saat Diterima</label>
                        <select name="item_condition" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                            <option value="baik">Baik (Sempurna)</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                            <option value="hilang">Hilang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Jumlah Denda Rp (Opsional)</label>
                        <input type="number" step="1000" name="penalty_amount" placeholder="0" 
                               class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">Catatan Admin</label>
                        <textarea name="note" rows="2" placeholder="Catatan fisik..." 
                                  class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
                        ✓ Konfirmasi Pengembalian (Selesai)
                    </button>
                </form>
            <?php else: ?>
                <p class="text-slate-500 text-xs">Transaksi peminjaman ini telah bersifat final (<?= e($loan['status']) ?>).</p>
            <?php endif; ?>
        </div>

        <!-- Audit Log Timeline -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800/80">
            <h3 class="text-sm font-bold text-white mb-4">Audit Log</h3>
            <div class="relative border-l-2 border-slate-800 ml-3 space-y-4 text-xs">
                <?php foreach ($loan['logs'] as $log): ?>
                    <div class="relative pl-5">
                        <div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-emerald-400"></div>
                        <div class="font-bold text-white"><?= e($log['to_status']) ?></div>
                        <div class="text-[10px] text-slate-500"><?= date('d M Y H:i', strtotime($log['created_at'])) ?> oleh <?= e($log['changed_by_name'] ?: 'Sistem') ?></div>
                        <?php if ($log['note']): ?>
                            <div class="text-[11px] text-slate-400 italic">"<?= e($log['note']) ?>"</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
