<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <h2 class="fw-bold" style="color: #2d3748;"><?= ($view_mode ?? 'pending') === 'warehouse' ? 'Gudang' : 'Penerimaan' ?></h2>
        <p class="text-muted"><?= ($view_mode ?? 'pending') === 'warehouse' ? 'Riwayat barang yang sudah diterima dan masuk ke gudang.' : 'Kelola Purchase Order yang menunggu diterima.' ?></p>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if (($view_mode ?? 'pending') === 'pending'): ?>
        <!-- Tabel PO Menunggu Penerimaan -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-4 border-top border-warning border-4">
                <div class="card-header bg-white pt-3 pb-0 border-0">
                    <h6 class="fw-bold text-warning mb-0"><i class="bi bi-clock-history"></i> PO Menunggu Diterima</h6>
                </div>
                <div class="card-body">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. PO</th>
                                <th>Tanggal PO</th>
                                <th>Produk Dibeli</th>
                                <th>Supplier</th>
                                <th>Total Item</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pending_pos)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada kiriman PO yang tertunda.</td></tr>
                            <?php else: ?>
                                <?php foreach ($pending_pos as $po): ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border"><?= $po['po_number'] ?></span></td>
                                        <td><?= date('d M Y', strtotime($po['po_date'])) ?></td>
                                        <td class="fw-semibold"><?= esc($po['product_names'] ?? '-') ?></td>
                                        <td class="fw-semibold"><?= $po['supplier_name'] ?></td>
                                        <td><?= $po['total_items'] ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('receivings/detail/'.$po['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                            <a href="<?= base_url('receivings/process/'.$po['id']) ?>" class="btn btn-sm btn-success rounded-pill px-3" onclick="return confirm('Proses terima barang? Stok akan ditambahkan ke Gudang.')">
                                                <i class="bi bi-box-arrow-in-down"></i> Terima Barang
                                            </a>
                                            <form action="<?= base_url('receivings/delete/'.$po['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus PO yang masih menunggu diterima ini?')">
                                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (($view_mode ?? 'pending') === 'warehouse'): ?>
        <?php $warehouseGroups = [
            ['title' => 'Gudang Bahan Baku & Custom', 'icon' => 'bi-box-seam', 'items' => $warehouse_materials ?? []],
            ['title' => 'Gudang Produk', 'icon' => 'bi-boxes', 'items' => $warehouse_products ?? []],
        ]; ?>
        <?php foreach ($warehouseGroups as $warehouseGroup): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 border-top border-success border-4 h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="fw-bold text-success mb-0"><i class="bi <?= esc($warehouseGroup['icon']) ?> me-1"></i><?= esc($warehouseGroup['title']) ?></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Update Terakhir</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($warehouseGroup['items'])): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat penerimaan barang.</td></tr>
                            <?php else: ?>
                                <?php foreach ($warehouseGroup['items'] as $rcv): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($rcv['stock_updated_at'])): ?>
                                                <div><?= date('d M Y', strtotime($rcv['stock_updated_at'])) ?></div>
                                                <small class="text-muted"><?= date('H:i', strtotime($rcv['stock_updated_at'])) ?> WIB</small>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-semibold"><?= esc($rcv['product_name']) ?></td>
                                        <td><?= esc($rcv['unit']) ?></td>
                                        <td><?= number_format($rcv['stock'], 0, ',', '.') ?></td>
                                        <td class="text-center text-nowrap">
                                            <?php if (!empty($rcv['stock_type']) && !empty($rcv['stock_id'])): ?>
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <a href="<?= base_url('receivings/warehouse/detail/' . $rcv['stock_type'] . '/' . $rcv['stock_id']) ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i>Detail
                                                    </a>
                                                    <form action="<?= base_url('receivings/warehouse/reduce/' . $rcv['stock_type'] . '/' . $rcv['stock_id']) ?>" method="post" class="d-inline-flex">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Kurangi 1 stok barang ini?')"><i class="bi bi-dash-circle me-1"></i>Kurangi </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?= $this->endSection() ?>