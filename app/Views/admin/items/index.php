<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-black text-white tracking-tight">Manajemen Alat Pendakian</h1>
        <p class="text-slate-400 text-xs mt-1">Kelola data perlengkapan, stok, harga sewa per hari, dan status kondisi fisik</p>
    </div>
    <a href="/admin/items/create" class="px-5 py-3 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
        + Tambah Alat Baru
    </a>
</div>

<div class="glass-panel rounded-3xl border border-slate-800/80 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-900/80 uppercase text-slate-400 font-bold border-b border-slate-800">
                <tr>
                    <th class="p-4">Alat</th>
                    <th class="p-4">Kategori</th>
                    <th class="p-4 text-center">Stok Fisik</th>
                    <th class="p-4 text-right">Harga / Hari</th>
                    <th class="p-4 text-center">Kondisi</th>
                    <th class="p-4 text-center">Status Visible</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
                <?php foreach ($items as $item): ?>
                    <tr class="hover:bg-slate-900/40 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center shrink-0 overflow-hidden">
                                    <?php if ($item['image_path']): ?>
                                        <img src="/<?= e($item['image_path']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <span>⛺</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="font-bold text-white text-sm"><?= e($item['name']) ?></div>
                                    <div class="text-[11px] text-slate-500">Slug: <?= e($item['slug']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-slate-300"><?= e($item['category_name']) ?></td>
                        <td class="p-4 text-center font-bold text-white"><?= (int)$item['stock_total'] ?> Unit</td>
                        <td class="p-4 text-right font-bold text-emerald-400"><?= rupiah($item['price_per_day']) ?></td>
                        <td class="p-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $item['condition_status'] === 'baik' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' ?>">
                                <?= e(ucwords(str_replace('_', ' ', $item['condition_status']))) ?>
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $item['is_active'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' ?>">
                                <?= $item['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <a href="/admin/items/<?= $item['id'] ?>/edit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-lg transition-all">
                                Edit
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
