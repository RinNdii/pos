<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #2d3748;">Input Data Barang</h2>
        <p class="text-muted mb-0">Tambahkan produk baru ke master barang.</p>
    </div>
    <a href="<?= base_url('items') ?>" class="btn btn-light border">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-primary text-white" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
        <i class="bi bi-box-seam me-2"></i>Detail Barang Baru
    </div>
    <div class="card-body p-4">
        <form action="<?= base_url('items/store') ?>" method="post" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="sku" class="form-label">SKU</label>
                    <input type="text" id="sku" name="sku" class="form-control bg-light text-muted" value="<?= esc($auto_sku) ?>" readonly>
                </div>
                <div class="col-md-6">
                    <label for="name" class="form-label">Nama Barang</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Masukkan nama barang" required>
                </div>
                <div class="col-md-4">
                    <label for="selling_price" class="form-label">Harga Jual</label>
                    <input type="number" id="selling_price" name="selling_price" class="form-control" placeholder="Masukkan harga jual" min="0" required>
                </div>
                <div class="col-md-4">
                    <label for="unit" class="form-label">Satuan</label>
                    <select id="unit" name="unit" class="form-select" required>
                        <option value="">-- Pilih Satuan --</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Box">Box</option>
                        <option value="Kg">Kg</option>
                        <option value="Pack">Pack</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="stock" class="form-label">Kuantitas</label>
                    <input type="number" id="stock" name="stock" class="form-control" placeholder="Masukkan stok" min="0" required>
                </div>
                <div class="col-12">
                    <label for="image" class="form-label">Gambar Produk <span class="text-muted fw-normal">(opsional)</span></label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                    <small class="text-muted">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?= base_url('items') ?>" class="btn btn-light border">Batal</a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Simpan Barang
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
