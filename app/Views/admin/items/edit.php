<div class="max-w-2xl mx-auto my-6">
    <div class="mb-6">
        <a href="/admin/items" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-emerald-400 transition-colors">
            ← Kembali ke Daftar Alat
        </a>
    </div>

    <div class="glass-panel p-8 rounded-3xl border border-slate-800/80 shadow-2xl">
        <h1 class="text-2xl font-black text-white tracking-tight mb-2">Edit Data Alat Pendakian</h1>
        <p class="text-slate-400 text-xs mb-8">Perbarui rincian alat, stok fisik, atau status keaktifan</p>

        <form action="/admin/items/<?= $item['id'] ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Kategori Alat</label>
                <select name="category_id" required class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>" <?= $item['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Nama Alat</label>
                <input type="text" name="name" value="<?= e($item['name']) ?>" required 
                       class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Deskripsi Lengkap</label>
                <textarea name="description" rows="4" 
                          class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500"><?= e($item['description']) ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Jumlah Stok Total</label>
                    <input type="number" name="stock_total" value="<?= (int)$item['stock_total'] ?>" min="0" required
                           class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Harga Sewa / Hari (Rp)</label>
                    <input type="number" step="1000" name="price_per_day" value="<?= (float)$item['price_per_day'] ?>" min="0" required
                           class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Kondisi Fisik</label>
                    <select name="condition_status" class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                        <option value="baik" <?= $item['condition_status'] === 'baik' ? 'selected' : '' ?>>Baik</option>
                        <option value="perlu_perbaikan" <?= $item['condition_status'] === 'perlu_perbaikan' ? 'selected' : '' ?>>Perlu Perbaikan</option>
                        <option value="rusak" <?= $item['condition_status'] === 'rusak' ? 'selected' : '' ?>>Rusak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Ganti Gambar (Maks 2MB, Opsional)</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                           class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-500 file:text-slate-950">
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" <?= $item['is_active'] ? 'checked' : '' ?> class="w-4 h-4 rounded text-emerald-500 focus:ring-emerald-500 bg-slate-900 border-slate-800">
                <label for="is_active" class="text-xs font-semibold text-white">Tampilkan di katalog publik (Aktif)</label>
            </div>

            <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-sm rounded-xl shadow-lg shadow-emerald-500/20 transition-all mt-4">
                Perbarui Data Alat
            </button>
        </form>
    </div>
</div>
