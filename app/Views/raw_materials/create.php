<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #2d3748;">Input Bahan Baku</h2>
        <p class="text-muted mb-0">Tambahkan bahan baku baru ke master.</p>
    </div>
    <a href="<?= base_url('raw_materials') ?>" class="btn btn-light border">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-primary text-white" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
        <i class="bi bi-box-seam me-2"></i>Detail Bahan Baku Baru
    </div>
    <div class="card-body p-4">
        <form action="<?= base_url('raw_materials/store') ?>" method="post">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="sku" class="form-label">SKU</label>
                    <input type="text" id="sku" name="sku" class="form-control bg-light text-muted" value="<?= esc($auto_sku) ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label for="name" class="form-label">Nama Bahan Baku</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama bahan baku" required>
                </div>
                <div class="col-md-6">
                    <label for="unit" class="form-label">Satuan</label>
                    <select id="unit" name="unit" class="form-select" required>
                        <option value="">-- Pilih Satuan --</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Box">Box</option>
                        <option value="Kg">Kg</option>
                        <option value="Pack">Pack</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?= base_url('raw_materials') ?>" class="btn btn-light border">Batal</a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Simpan Bahan Baku
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
