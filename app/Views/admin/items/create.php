<div class="max-w-2xl mx-auto my-6">
    <div class="mb-6">
        <a href="/admin/items" class="inline-flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-emerald-400 transition-colors">
            ← Kembali ke Daftar Alat
        </a>
    </div>

    <div class="glass-panel p-8 rounded-3xl border border-slate-800/80 shadow-2xl">
        <h1 class="text-2xl font-black text-white tracking-tight mb-2">Tambah Alat Pendakian Baru</h1>
        <p class="text-slate-400 text-xs mb-8">Isi data perlengkapan outdoor untuk ditampilkan pada katalog persewaan</p>

        <form action="/admin/items" method="POST" enctype="multipart/form-data" class="space-y-5">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Kategori Alat</label>
                <select name="category_id" required class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>" <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Nama Alat</label>
                <input type="text" name="name" value="<?= e(old('name')) ?>" required placeholder="Misal: Tenda Eiger Kapasitas 4 Orang"
                       class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Deskripsi Lengkap</label>
                <textarea name="description" rows="4" placeholder="Detail spesifikasi alat..." 
                          class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500"><?= e(old('description')) ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Jumlah Stok Total</label>
                    <input type="number" name="stock_total" value="<?= e(old('stock_total', 1)) ?>" min="0" required
                           class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Harga Sewa / Hari (Rp)</label>
                    <input type="number" step="1000" name="price_per_day" value="<?= e(old('price_per_day', 0)) ?>" min="0" required
                           class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Kondisi Fisik</label>
                    <select name="condition_status" class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                        <option value="baik">Baik</option>
                        <option value="perlu_perbaikan">Perlu Perbaikan</option>
                        <option value="rusak">Rusak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Gambar Alat (Maks 2MB, JPG/PNG/WebP)</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                           class="w-full px-3 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-500 file:text-slate-950">
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked class="w-4 h-4 rounded text-emerald-500 focus:ring-emerald-500 bg-slate-900 border-slate-800">
                <label for="is_active" class="text-xs font-semibold text-white">Tampilkan di katalog publik (Aktif)</label>
            </div>

            <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-sm rounded-xl shadow-lg shadow-emerald-500/20 transition-all mt-4">
                Simpan Alat Baru
            </button>
        </form>
    </div>
</div>
