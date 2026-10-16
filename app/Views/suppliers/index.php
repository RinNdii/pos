<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: #2d3748;">Master Supplier</h2>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <!-- Form Tambah Supplier -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-primary text-white" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
            Tambah Supplier Baru
        </div>
        <div class="card-body">
            <form action="<?= base_url('suppliers/store') ?>" method="post">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control" placeholder="Nama Supplier / Toko" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="phone" class="form-control" placeholder="No Telepon" required
                        onkeypress="return event.charCode >= 48 && event.charCode <= 57" 
       oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="address" class="form-control" placeholder="Alamat Lengkap" required>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-save"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Supplier -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Nama Supplier</th>
                        <th>No. Telepon</th>
                        <th>Alamat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suppliers)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data supplier.</td></tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td class="ps-3"><?= $supplier['id'] ?></td>
                                <td class="fw-semibold"><?= $supplier['name'] ?></td>
                                <td><?= $supplier['phone'] ?></td>
                                <td><?= $supplier['address'] ?></td>
                                <td class="text-center">
                                    <!-- Tombol Edit Modal -->
                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#editModal<?= $supplier['id'] ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <!-- Tombol Hapus -->
                                    <a href="<?= base_url('suppliers/delete/'.$supplier['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus supplier ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Modal Edit Supplier -->
                            <div class="modal fade" id="editModal<?= $supplier['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="<?= base_url('suppliers/update/'.$supplier['id']) ?>" method="post">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Supplier</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Supplier</label>
                                                    <input type="text" name="name" class="form-control" value="<?= $supplier['name'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">No. WhatsApp</label>
                                                    <input type="text" name="no_wa" class="form-control" placeholder="Contoh: 081234567890" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Alamat</label>
                                                    <textarea name="address" class="form-control" rows="3" required><?= $supplier['address'] ?></textarea>
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
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->endSection() ?>