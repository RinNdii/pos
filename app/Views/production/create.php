<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #2d3748;">Buat Produksi</h2>
        <p class="text-muted mb-0">Kurangi stok bahan baku untuk menambah stok produk jadi.</p>
    </div>
    <a href="<?= base_url('production') ?>" class="btn btn-light border rounded-pill px-4">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-4"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<form action="<?= base_url('production/store') ?>" method="post">
    <?= csrf_field() ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Produk</label>
                    <select name="item_id" class="form-select" required>
                        <option value="">Pilih Produk</option>
                        <?php foreach ($items as $item): ?>
                            <option value="<?= $item['id'] ?>"><?= esc($item['name']) ?> (<?= esc($item['unit']) ?>) - Stok: <?= (int) ($item['stock'] ?? 0) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jumlah Hasil Produksi</label>
                    <input type="number" name="qty" class="form-control" min="1" required placeholder="Contoh: 10">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="notes" class="form-control" placeholder="Opsional">
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-muted mb-0">Bahan Baku yang Digunakan</h6>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addMaterialRow()">
                    <i class="bi bi-plus"></i> Tambah Bahan
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Bahan Baku</th>
                            <th width="170">Stok Tersedia</th>
                            <th width="170">Qty Dipakai</th>
                            <th width="80" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="material_list">
                        <tr>
                            <td>
                                <div class="d-flex flex-column gap-2">
                                    <select name="raw_id[]" class="form-select raw-material-select" required>
                                        <option value="">Pilih bahan dari gudang</option>
                                        <option value="manual">Input Manual</option>
                                        <?php foreach ($raw_materials as $raw): ?>
                                            <?php $value = ($raw['type'] ?? 'raw_material') === 'custom' ? 'custom:' . rawurlencode((string) $raw['name']) . '|' . rawurlencode((string) $raw['unit']) : (string) ($raw['id'] ?? ''); ?>
                                            <option value="<?= esc($value) ?>" data-stock="<?= (int) ($raw['stock'] ?? 0) ?>" <?= ((int) ($raw['stock'] ?? 0) <= 0) ? 'disabled' : '' ?>><?= esc($raw['name']) ?> (<?= esc($raw['unit']) ?>) - stok gudang: <?= (int) ($raw['stock'] ?? 0) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="manual-material-fields d-none">
                                        <input type="text" name="raw_material_name_manual[]" class="form-control form-control-sm mb-2" placeholder="Nama bahan baku manual">
                                        <input type="text" name="raw_material_unit_manual[]" class="form-control form-control-sm" placeholder="Satuan (contoh: kg, pcs, box)">
                                    </div>
                                </div>
                            </td>
                            <td><span class="stock-display small text-muted fw-semibold">0</span></td>
                            <td><input type="number" name="raw_qty[]" class="form-control qty-used" min="0.01" step="0.01" placeholder="0" required></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success px-4 rounded-pill">
                    <i class="bi bi-save me-1"></i> Simpan Produksi
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    function addMaterialRow() {
        let options = `<option value="">Pilih bahan dari gudang</option><option value="manual">Input Manual</option>`;
        <?php foreach ($raw_materials as $raw): ?>
            <?php $value = ($raw['type'] ?? 'raw_material') === 'custom' ? 'custom:' . rawurlencode((string) $raw['name']) . '|' . rawurlencode((string) $raw['unit']) : (string) ($raw['id'] ?? ''); ?>
            options += `<option value="<?= esc($value) ?>" data-stock="<?= (int) ($raw['stock'] ?? 0) ?>" <?= ((int) ($raw['stock'] ?? 0) <= 0) ? 'disabled' : '' ?>><?= esc($raw['name']) ?> (<?= esc($raw['unit']) ?>) - stok gudang: <?= (int) ($raw['stock'] ?? 0) ?></option>`;
        <?php endforeach; ?>

        const tr = `
            <tr>
                <td>
                    <div class="d-flex flex-column gap-2">
                        <select name="raw_id[]" class="form-select raw-material-select" required>
                            <option value="">Pilih bahan dari gudang</option>
                            <option value="manual">Input Manual</option>
                            ${options}
                        </select>
                        <div class="manual-material-fields d-none">
                            <input type="text" name="raw_material_name_manual[]" class="form-control form-control-sm mb-2" placeholder="Nama bahan baku manual">
                            <input type="text" name="raw_material_unit_manual[]" class="form-control form-control-sm" placeholder="Satuan (contoh: kg, pcs, box)">
                        </div>
                    </div>
                </td>
                <td><span class="stock-display small text-muted fw-semibold">0</span></td>
                <td><input type="number" name="raw_qty[]" class="form-control qty-used" min="0.01" step="0.01" placeholder="0" required></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
            </tr>
        `;

        document.getElementById('material_list').insertAdjacentHTML('beforeend', tr);
    }

    document.addEventListener('change', function (event) {
        if (event.target.classList.contains('raw-material-select')) {
            const selected = event.target.selectedOptions[0];
            const row = event.target.closest('tr');
            const display = row.querySelector('.stock-display');
            const manualFields = row.querySelector('.manual-material-fields');

            if (selected && selected.value === 'manual') {
                manualFields.classList.remove('d-none');
                display.textContent = 'manual';
                display.className = 'stock-display small fw-semibold text-warning';
                return;
            }

            if (selected && Number(selected.dataset.stock) <= 0) {
                event.target.value = '';
                display.textContent = Number(selected.dataset.stock || 0);
                display.className = 'stock-display small fw-semibold text-danger';
                return;
            }

            const stock = selected && selected.dataset.stock ? selected.dataset.stock : 0;
            manualFields.classList.add('d-none');
            display.textContent = stock;
            display.className = 'stock-display small fw-semibold ' + (Number(stock) <= 0 ? 'text-danger' : 'text-success');
        }
    });
</script>
<?= $this->endSection() ?>
