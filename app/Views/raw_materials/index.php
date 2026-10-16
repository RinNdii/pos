<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold" style="color: #2d3748;">Master Bahan Baku</h3>
    <a href="<?= base_url('raw_materials/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Input Data Baru
    </a>
</div>

 <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <!-- Tabel Daftar Bahan Baku -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">SKU</th>
                        <th>Nama Bahan Baku</th>
                        <th>Satuan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- UBAH: Gunakan variabel $raw_materials sesuai controller Anda -->
                    <?php if (empty($raw_materials)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data bahan baku.</td></tr>
                    <?php else: ?>
                        <?php foreach ($raw_materials as $item): ?>
                            <tr>
                                <td class="ps-3"><span class="badge bg-light text-dark border"><?= $item['sku'] ?? '-' ?></span></td>
                                <td class="fw-semibold"><?= $item['name'] ?></td>
                                <td><?= $item['unit'] ?? '-' ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editModal<?= $item['id'] ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <!-- UBAH: Arahkan href ke raw_materials/delete -->
                                    <a href="<?= base_url('raw_materials/delete/'.$item['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus bahan baku ini?')">
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

    <?php if (!empty($raw_materials)): ?>
        <?php foreach ($raw_materials as $item): ?>
            <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <!-- UBAH: Arahkan action ke raw_materials/update -->
                    <form action="<?= base_url('raw_materials/update/'.$item['id']) ?>" method="post">
                        <div class="modal-content border-0 rounded-4 shadow">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Edit Bahan Baku - <?= $item['sku'] ?? '' ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nama Bahan Baku</label>
                                    <input type="text" name="name" class="form-control" value="<?= $item['name'] ?>" required>
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