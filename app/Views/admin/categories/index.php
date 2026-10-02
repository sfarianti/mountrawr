<div class="mb-8">
    <h1 class="text-3xl font-black text-white tracking-tight">Manajemen Kategori Alat</h1>
    <p class="text-slate-400 text-xs mt-1">Kelola kelompok kategori perlengkapan pendakian gunung</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Category Creation Form -->
    <div class="lg:col-span-5">
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-slate-800/80">
            <h2 class="text-lg font-bold text-white mb-4">Tambah Kategori Baru</h2>

            <form action="/admin/categories" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Nama Kategori</label>
                    <input type="text" name="name" required placeholder="Misal: Tenda & Shelter" 
                           class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" placeholder="Keterangan..." 
                              class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <button type="submit" class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
                    Simpan Kategori Baru
                </button>
            </form>
        </div>
    </div>

    <!-- Category List Table -->
    <div class="lg:col-span-7">
        <div class="glass-panel rounded-3xl border border-slate-800/80 overflow-hidden">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 uppercase text-slate-400 font-bold border-b border-slate-800">
                    <tr>
                        <th class="p-4">Nama Kategori</th>
                        <th class="p-4">Slug</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-white text-sm"><?= e($cat['name']) ?></div>
                                <div class="text-[11px] text-slate-500 line-clamp-1"><?= e($cat['description']) ?></div>
                            </td>
                            <td class="p-4 font-mono text-slate-400 text-[11px]"><?= e($cat['slug']) ?></td>
                            <td class="p-4 text-center">
                                <form action="/admin/categories/<?= $cat['id'] ?>/delete" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 font-bold text-[11px] rounded-lg border border-rose-500/30 transition-all">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
