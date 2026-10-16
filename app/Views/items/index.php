<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: #2d3748;">Master Produk</h2>
        <a href="<?= base_url('items/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Input Data Baru
        </a>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <!-- Tabel Daftar Barang -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">SKU</th>
                        <th>Nama Barang</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data barang.</td></tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="ps-3"><span class="badge bg-light text-dark border"><?= $item['sku'] ?></span></td>
                                <td class="fw-semibold"><?= $item['name'] ?></td>
                                
                                <td>Rp <?= number_format($item['selling_price'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge <?= $item['stock'] == 0 ? 'bg-danger' : 'bg-success' ?>">
                                        <?= $item['stock'] ?>
                                    </span>
                                </td>
                                <td><?= $item['unit'] ?? '-'?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editModal<?= $item['id'] ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <a href="<?= base_url('items/delete/'.$item['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
            <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="<?= base_url('items/update/'.$item['id']) ?>" method="post" enctype="multipart/form-data">
                        <div class="modal-content border-0 rounded-4 shadow">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Edit Barang - <?= $item['sku'] ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nama Barang</label>
                                    <input type="text" name="name" class="form-control" value="<?= $item['name'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Harga Jual</label>
                                    <input type="number" name="selling_price" class="form-control" value="<?= $item['selling_price'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stok (Qty)</label>
                                    <input type="number" name="stock" class="form-control" value="<?= $item['stock'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Satuan</label>
                                    <select name="unit" class="form-select" required>
    <option value="Pcs" <?= (isset($item['unit']) && $item['unit'] == 'Pcs') ? 'selected' : '' ?>>Pcs</option>
    <option value="Box" <?= (isset($item['unit']) && $item['unit'] == 'Box') ? 'selected' : '' ?>>Box</option>
    <option value="Kg" <?= (isset($item['unit']) && $item['unit'] == 'Kg') ? 'selected' : '' ?>>Kg</option>
    <option value="Pack" <?= (isset($item['unit']) && $item['unit'] == 'Pack') ? 'selected' : '' ?>>Pack</option>
</select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Gambar Produk</label>
                                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                                    <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->endSection() ?>