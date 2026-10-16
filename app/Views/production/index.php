<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #2d3748;">Produksi</h2>
        <p class="text-muted mb-0">Catat penggunaan bahan baku untuk menambah stok produk.</p>
    </div>
    <a href="<?= base_url('production/create') ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Buat Produksi
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-4"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Tanggal</th>
                        <th>Produk</th>
                        <th>Hasil</th>
                        <th>Bahan Baku Dipakai</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productions)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data produksi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productions as $production): ?>
                            <tr>
                                <td class="ps-3"><?= date('d M Y', strtotime($production['created_at'])) ?></td>
                                <td class="fw-semibold"><?= esc($production['item_name'] ?? '-') ?></td>
                                <td><?= number_format((int) ($production['qty'] ?? 0), 0, ',', '.') ?> <?= esc($production['item_unit'] ?? '') ?></td>
                                <td>
                                    <?php if (!empty($production['materials'])): ?>
                                        <?php foreach ($production['materials'] as $material): ?>
                                            <div class="small">
                                                <?= esc($material['raw_material_name'] ?? '-') ?>: <?= number_format((float) ($material['qty_used'] ?? 0), 0, ',', '.') ?> <?= esc($material['raw_material_unit'] ?? '') ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($production['notes'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
