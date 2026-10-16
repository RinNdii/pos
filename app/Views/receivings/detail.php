<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #2d3748;">Detail PO</h2>
        <p class="text-muted mb-0">Daftar barang yang dikirim dari pembelian.</p>
    </div>
    <a href="<?= base_url('receivings') ?>" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">No. PO</div>
                <div class="fw-bold"><?= esc($purchase['po_number']) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Tanggal PO</div>
                <div class="fw-bold"><?= date('d M Y', strtotime($purchase['po_date'])) ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Produk yang Dibeli</div>
                <div class="fw-bold"><?= esc($product_names ?? '-') ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Supplier</div>
                <div class="fw-bold"><?= esc($purchase['supplier_name'] ?? 'Supplier Terhapus') ?></div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Status</div>
                <span class="badge bg-warning text-dark">Dipesan</span>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="p-3 border-bottom">
            <h6 class="fw-bold mb-0">Barang yang Dikirim</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No.</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Kuantitas</th>
                        <th class="text-end pe-3">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($details)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada detail barang pada PO ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($details as $index => $detail): ?>
                            <tr>
                                <td class="ps-3"><?= $index + 1 ?></td>
                                <td class="fw-semibold"><?= esc($detail['display_name']) ?></td>
                                <td><?= esc($detail['display_unit']) ?></td>
                                <td class="text-end">Rp <?= number_format($detail['purchase_price'], 0, ',', '.') ?></td>
                                <td class="text-end"><?= number_format($detail['qty'], 0, ',', '.') ?></td>
                                <td class="text-end pe-3 fw-semibold">Rp <?= number_format($detail['line_total'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-light fw-bold">
                            <td colspan="5" class="text-end">Total Keseluruhan</td>
                            <td class="text-end pe-3">Rp <?= number_format($total_purchase, 0, ',', '.') ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-3 text-end">
            <a href="<?= base_url('receivings/process/'.$purchase['id']) ?>" class="btn btn-success" onclick="return confirm('Proses terima barang? Stok akan ditambahkan ke Gudang.')">
                <i class="bi bi-box-arrow-in-down me-1"></i> Terima Barang
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
