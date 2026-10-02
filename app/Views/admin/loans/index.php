<div class="mb-8">
    <h1 class="text-3xl font-black text-white tracking-tight">Manajemen Peminjaman (Admin)</h1>
    <p class="text-slate-400 text-xs mt-1">Kelola persetujuan, penyerahan, dan konfirmasi pengembalian alat pendakian</p>
</div>

<!-- Quick Stats Cards (Fase 7 Dashboard) -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <a href="/admin/loans?status=pending" class="glass-panel p-6 rounded-3xl border border-slate-800 hover:border-amber-500/50 transition-all flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Menunggu Approval</span>
            <span class="text-3xl font-black text-amber-400 mt-1 block"><?= $pendingCount ?></span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl font-bold">
            ⏳
        </div>
    </a>

    <a href="/admin/loans?status=borrowed" class="glass-panel p-6 rounded-3xl border border-slate-800 hover:border-blue-500/50 transition-all flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Sedang Dipinjam</span>
            <span class="text-3xl font-black text-blue-400 mt-1 block"><?= $borrowedCount ?></span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xl font-bold">
            🎒
        </div>
    </a>

    <a href="/admin/loans?status=borrowed" class="glass-panel p-6 rounded-3xl border border-slate-800 hover:border-rose-500/50 transition-all flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-400 block uppercase tracking-wider">Jatuh Tempo Hari Ini</span>
            <span class="text-3xl font-black text-rose-400 mt-1 block"><?= $dueTodayCount ?></span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xl font-bold">
            📅
        </div>
    </a>
</div>

<!-- Status Filters -->
<div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-4 mb-8">
    <a href="/admin/loans" class="px-4 py-2 rounded-xl text-xs font-bold <?= empty($currentStatus) ? 'bg-emerald-500 text-slate-950' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
        Semua Status
    </a>
    <a href="/admin/loans?status=pending" class="px-4 py-2 rounded-xl text-xs font-bold <?= $currentStatus === 'pending' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
        Pending
    </a>
    <a href="/admin/loans?status=approved" class="px-4 py-2 rounded-xl text-xs font-bold <?= $currentStatus === 'approved' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
        Approved
    </a>
    <a href="/admin/loans?status=borrowed" class="px-4 py-2 rounded-xl text-xs font-bold <?= $currentStatus === 'borrowed' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
        Borrowed
    </a>
    <a href="/admin/loans?status=returned" class="px-4 py-2 rounded-xl text-xs font-bold <?= $currentStatus === 'returned' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
        Returned
    </a>
    <a href="/admin/loans?status=completed" class="px-4 py-2 rounded-xl text-xs font-bold <?= $currentStatus === 'completed' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' ?>">
        Completed
    </a>
</div>

<!-- Admin Loans Table -->
<?php if (empty($loans)): ?>
    <div class="glass-panel p-12 rounded-3xl border border-slate-800 text-center my-8">
        <div class="text-4xl mb-3">📦</div>
        <h3 class="text-lg font-bold text-white mb-1">Tidak Ada Data Peminjaman</h3>
        <p class="text-slate-400 text-xs">Tidak ada transaksi peminjaman untuk filter status ini.</p>
    </div>
<?php else: ?>
    <div class="glass-panel rounded-3xl border border-slate-800/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 uppercase text-slate-400 font-bold border-b border-slate-800">
                    <tr>
                        <th class="p-4">Kode / Peminjam</th>
                        <th class="p-4">Tanggal Sewa</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Total Biaya</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <?php foreach ($loans as $loan): ?>
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
                            $isOverdue = ($loan['status'] === 'borrowed' && $loan['end_date'] < date('Y-m-d'));
                        ?>
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="p-4">
                                <div class="font-mono font-bold text-white text-sm"><?= e($loan['code']) ?></div>
                                <div class="text-slate-400 mt-0.5"><?= e($loan['user_name']) ?> (<?= e($loan['user_email']) ?>)</div>
                            </td>
                            <td class="p-4 text-slate-300">
                                <div><?= date('d M Y', strtotime($loan['start_date'])) ?> — <?= date('d M Y', strtotime($loan['end_date'])) ?></div>
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full border text-[10px] font-bold uppercase <?= $color ?>">
                                    <?= e($loan['status']) ?>
                                </span>
                                <?php if ($isOverdue): ?>
                                    <span class="ml-2 px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 text-[10px] font-bold animate-pulse">
                                        Terlambat
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-right font-bold text-emerald-400">
                                <?= rupiah($loan['total_price']) ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="/admin/loans/<?= $loan['id'] ?>" class="px-4 py-2 bg-slate-800 hover:bg-emerald-500 hover:text-slate-950 text-white font-bold text-xs rounded-xl transition-all inline-block">
                                    Kelola →
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
