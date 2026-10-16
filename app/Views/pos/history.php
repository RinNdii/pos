<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <h2 class="fw-bold" style="color: #283593;">Riwayat Penjualan</h2>
        <p class="text-muted">Data rekapitulasi seluruh transaksi yang telah diproses oleh Kasir.</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('info')): ?>
        <div class="alert alert-info border-0 shadow-sm"><?= esc(session()->getFlashdata('info')) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No. Invoice</th>
                        <th>Tanggal Transaksi</th>
                        <th>Total Belanja</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sales)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat transaksi penjualan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($sales as $row): ?>
                            <?php $reportExists = db_connect()->table('sale_material_reports')->where('sale_id', $row['id'])->countAllResults() > 0; ?>
                            <tr>
                                <td class="ps-4"><a href="<?= base_url('pos/history/detail/' . $row['id']) ?>" class="badge bg-light text-dark border text-decoration-none"><?= esc($row['invoice_number']) ?></a></td>
                                <td><?= date('d M Y, H:i', strtotime($row['sale_date'])) ?></td>
                                <td class="fw-bold text-success">Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="<?= base_url('pos/history/detail/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Detail</a>
                                        <?php if ($reportExists): ?>
                                            <button type="button" class="btn btn-sm btn-success" disabled>
                                                <i class="bi bi-check-circle me-1"></i>Selesai
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#materialModal<?= $row['id'] ?>">
                                                <i class="bi bi-box-seam me-1"></i>ACC
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <?php if (!$reportExists): ?>
                            <div class="modal fade" id="materialModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="materialModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-4">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold" id="materialModalLabel<?= $row['id'] ?>">Laporan Bahan - <?= esc($row['invoice_number']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <form action="<?= base_url('pos/report/' . $row['id']) ?>" method="post">
                                            <div class="modal-body">
                                                <?= csrf_field() ?>
                                                <?php
                                                    $details = db_connect()->table('sale_details')
                                                        ->select('sale_details.*, items.name as item_name')
                                                        ->join('items', 'items.id = sale_details.item_id', 'left')
                                                        ->where('sale_details.sale_id', $row['id'])
                                                        ->get()->getResultArray();

                                                    $rawMaterialWarehouse = db_connect()->table('raw_materials')
                                                        ->select('raw_materials.id as raw_material_id, raw_materials.name, raw_materials.unit, raw_materials.stock as warehouse_stock, NULL as custom_name, NULL as custom_unit')
                                                        ->join('purchase_details', 'purchase_details.raw_material_id = raw_materials.id')
                                                        ->where('purchase_details.item_id IS NULL', null, false)
                                                        ->groupBy('raw_materials.id, raw_materials.name, raw_materials.unit, raw_materials.stock')
                                                        ->orderBy('raw_materials.name', 'ASC')
                                                        ->get()->getResultArray();

                                                    $manualWarehouseItems = db_connect()->table('purchase_details')
                                                        ->select('NULL as raw_material_id, raw_materials.name, raw_materials.unit, raw_materials.stock as warehouse_stock, raw_materials.name as custom_name, raw_materials.unit as custom_unit')
                                                        ->join('raw_materials', 'raw_materials.name = purchase_details.product_name AND raw_materials.unit = purchase_details.unit')
                                                        ->where('purchase_details.item_id IS NULL', null, false)
                                                        ->where('purchase_details.raw_material_id IS NULL', null, false)
                                                        ->where('purchase_details.product_name IS NOT NULL', null, false)
                                                        ->groupBy('raw_materials.id, raw_materials.name, raw_materials.unit, raw_materials.stock')
                                                        ->orderBy('purchase_details.product_name', 'ASC')
                                                        ->get()->getResultArray();

                                                    $warehouseItems = array_merge($rawMaterialWarehouse, $manualWarehouseItems);
                                                    usort($warehouseItems, function ($a, $b) {
                                                        return strcmp(($a['name'] ?? ''), ($b['name'] ?? ''));
                                                    });
                                                ?>

                                                <?php foreach ($details as $detail): ?>
                                                    <div class="border rounded-3 p-3 mb-3">
                                                        <div class="fw-semibold mb-2"><?= esc($detail['item_name'] ?? 'Produk') ?></div>
                                                        <input type="hidden" name="item_id[]" value="<?= (int) ($detail['item_id'] ?? 0) ?>">
                                                        <input type="hidden" name="item_name[]" value="<?= esc($detail['item_name'] ?? '') ?>">
                                                        <div class="row g-2 align-items-end">
                                                            <div class="col-md-7">
                                                                <label class="form-label small text-muted">Bahan dari gudang</label>
                                                                <select name="raw_id[]" class="form-select form-select-sm" required>
                                                                    <option value="">Pilih bahan dari gudang</option>
                                                                    <?php foreach ($warehouseItems as $raw): ?>
                                                                        <?php $stock = (int) ($raw['warehouse_stock'] ?? 0); ?>
                                                                        <?php $value = isset($raw['raw_material_id']) && $raw['raw_material_id'] !== null ? (string) (int) $raw['raw_material_id'] : 'custom:' . rawurlencode((string) ($raw['custom_name'] ?? $raw['name'])) . '|' . rawurlencode((string) ($raw['custom_unit'] ?? $raw['unit'])); ?>
                                                                        <option value="<?= esc($value) ?>" <?= $stock <= 0 ? 'disabled' : '' ?>><?= esc($raw['name']) ?> (<?= esc($raw['unit']) ?>) - stok gudang: <?= $stock ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <label class="form-label small text-muted">Qty dipakai</label>
                                                                <input type="number" name="raw_qty[]" class="form-control form-control-sm" min="0.01" step="0.01" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>

                                                <?php if (empty($details)): ?>
                                                    <div class="text-muted small">Tidak ada produk di transaksi ini.</div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <?php if (!empty($details)): ?>
                                                    <button type="submit" class="btn btn-success rounded-pill px-4">
                                                        <i class="bi bi-save me-1"></i> Simpan
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?= $this->endSection() ?>