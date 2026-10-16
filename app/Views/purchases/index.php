<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color: #2d3748;">Data Pembelian (PO)</h2>
        <a href="<?= base_url('purchases/create') ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Buat PO Baru
        </a>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No. PO</th>
                        <th>Tanggal</th>
                        <th>Produk Dibeli</th>
                        <th>Supplier</th>
                        <th>Total Item</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($purchases)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data pembelian.</td></tr>
                    <?php else: ?>
                        <?php foreach ($purchases as $po): ?>
                            <tr>
                                <td class="ps-4"><span class="badge bg-light text-dark border"><?= $po['po_number'] ?></span></td>
                                <td><?= date('d M Y', strtotime($po['po_date'])) ?></td>
                                <td class="fw-semibold"><?= esc($po['product_names'] ?? '-') ?></td>
                                <td class="fw-semibold"><?= $po['supplier_name'] ?? 'Supplier Terhapus' ?></td>
                                <td><?= $po['total_items'] ?></td>
                                <td>
                                    <span class="badge <?= $po['status'] == 'Pending' ? 'bg-warning text-dark' : 'bg-success' ?>">
                                        <?= $po['status'] == 'Pending' ? 'Dipesan' : $po['status'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?= $this->endSection() ?>