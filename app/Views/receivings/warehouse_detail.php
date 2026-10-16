<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <a href="<?= base_url('warehouse') ?>" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i>Kembali ke gudang</a>
        <h2 class="fw-bold mt-2 mb-1" style="color: #2d3748;">Detail Penerimaan Barang</h2>
        <p class="text-muted mb-0"><?= esc($stock_item['name']) ?> (<?= esc($stock_item['unit']) ?>)</p>
    </div>
    <div class="text-end">
        <div class="small text-muted">Stok Akhir</div>
        <div class="fs-4 fw-bold text-success"><?= number_format($stock_item['stock'], 0, ',', '.') ?></div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0 text-success"><i class="bi bi-box-arrow-in-down me-2"></i>Barang Masuk</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Tanggal Masuk</th>
                    <th class="text-end">Harga Masuk</th>
                    <th class="text-end pe-4">Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($receiving_history)): ?>
                    <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada barang masuk.</td></tr>
                <?php else: ?>
                    <?php foreach ($receiving_history as $receiving): ?>
                        <tr>
                            <td class="ps-4"><?= date('d M Y', strtotime($receiving['receive_date'])) ?></td>
                            <td class="text-end">Rp <?= number_format($receiving['purchase_price'], 0, ',', '.') ?></td>
                            <td class="text-end pe-4">+<?= number_format($receiving['quantity'], 0, ',', '.') ?> <?= esc($stock_item['unit']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-box-arrow-up me-2"></i>Barang Keluar</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <?php if ($outgoing_is_material_report ?? false): ?>
                        <th class="ps-4">Produk Tujuan</th>
                        <th>Tanggal Penggunaan</th>
                    <?php else: ?>
                        <th class="ps-4">No. Invoice</th>
                        <th>Tanggal Keluar</th>
                        <th class="text-end">Harga Satuan</th>
                    <?php endif; ?>
                    <th class="text-end">Bahan terpakai</th>
                    <?php if (!($outgoing_is_material_report ?? false)): ?>
                        <th class="text-end pe-4">Subtotal</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sales_history)): ?>
                    <tr><td colspan="<?= ($outgoing_is_material_report ?? false) ? '3' : '5' ?>" class="text-center py-4 text-muted">Belum ada barang keluar.</td></tr>
                <?php else: ?>
                    <?php foreach ($sales_history as $sale): ?>
                        <tr>
                            <?php if ($outgoing_is_material_report ?? false): ?>
                                <td class="ps-4 fw-semibold text-danger"><?= esc($sale['item_name']) ?></td>
                                <td><?= date('d M Y, H:i', strtotime($sale['report_date'])) ?></td>
                            <?php else: ?>
                                <td class="ps-4 fw-semibold text-danger"><?= esc($sale['invoice_number']) ?></td>
                                <td><?= date('d M Y, H:i', strtotime($sale['sale_date'])) ?></td>
                                <td class="text-end">Rp <?= number_format($sale['price'], 0, ',', '.') ?></td>
                            <?php endif; ?>
                            <td class="text-end">-<?= number_format($sale['qty'], 0, ',', '.') ?> <?= esc($stock_item['unit']) ?></td>
                            <?php if (!($outgoing_is_material_report ?? false)): ?>
                                <td class="text-end pe-4">Rp <?= number_format($sale['subtotal'], 0, ',', '.') ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>