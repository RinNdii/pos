<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <a href="<?= base_url('pos/history') ?>" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i>Kembali ke riwayat</a>
        <h2 class="fw-bold mt-2 mb-1" style="color: #283593;">Detail Penjualan</h2>
        <p class="text-muted mb-0">Invoice <?= esc($sale['invoice_number']) ?></p>
    </div>
    <span class="badge bg-primary-subtle text-primary fs-6"><?= esc($sale['payment_method'] ?? 'Tunai') ?></span>
</div>

<div class="row g-3">
    <div class="col-lg-8 order-lg-1">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Produk yang Dibeli</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Produk</th>
                            <th>Satuan</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end pe-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $detail): ?>
                            <tr>
                                <td class="ps-4 fw-semibold"><?= esc($detail['item_name'] ?? 'Produk dihapus') ?></td>
                                <td><?= esc($detail['unit'] ?? '-') ?></td>
                                <td class="text-end">Rp <?= number_format($detail['price'], 0, ',', '.') ?></td>
                                <td class="text-center"><?= esc($detail['qty']) ?></td>
                                <td class="text-end pe-4">Rp <?= number_format($detail['subtotal'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mt-2">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Bahan yang Dipakai</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Bahan</th>
                            <th>Satuan</th>
                            <th class="text-end pe-4">Qty Dipakai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($material_reports)): ?>
                            <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada laporan bahan untuk transaksi ini.</td></tr>
                        <?php else: ?>
                            <?php foreach ($material_reports as $material): ?>
                                <tr>
                                    <td class="ps-4 fw-semibold"><?= esc($material['raw_material_name']) ?></td>
                                    <td><?= esc($material['raw_material_unit'] ?? '-') ?></td>
                                    <td class="text-end pe-4"><?= number_format($material['qty_used'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4 order-lg-2">
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Informasi Transaksi</h5>
                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">No. Invoice</span><strong><?= esc($sale['invoice_number']) ?></strong></div>
                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Tanggal</span><strong><?= date('d M Y, H:i', strtotime($sale['sale_date'])) ?></strong></div>
                <div class="d-flex justify-content-between small"><span class="text-muted">Pelanggan</span><strong><?= esc($sale['customer_name'] ?? 'Umum') ?></strong></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Ringkasan Pembayaran</h5>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subtotal</span><span>Rp <?= number_format($sale['total_amount'] + ($sale['discount'] ?? 0), 0, ',', '.') ?></span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Diskon</span><span>Rp <?= number_format($sale['discount'] ?? 0, 0, ',', '.') ?></span></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span class="text-success">Rp <?= number_format($sale['total_amount'], 0, ',', '.') ?></span></div>
                <hr>
                <div class="small text-muted">Metode Pembayaran</div>
                <div class="fw-semibold"><?= esc($sale['payment_method'] ?? 'Tunai') ?></div>
                <?php if (($sale['payment_method'] ?? '') === 'Transfer'): ?>
                    <div class="small text-muted mt-2">Bank Transfer</div>
                    <div class="fw-semibold"><?= esc($sale['transfer_bank'] ?? '-') ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>