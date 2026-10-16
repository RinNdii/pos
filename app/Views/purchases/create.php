<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <h2 class="fw-bold" style="color: #2d3748;">Buat Purchase Order Baru</h2>
    </div>

    <form action="<?= base_url('purchases/store') ?>" method="post">
        <div class="row g-4">
            <!-- Form Header PO -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold text-muted mb-3">Informasi Pesanan</h6>
                        <div class="mb-3">
                            <label class="form-label">Nomor PO</label>
                            <input type="text" name="po_number" class="form-control bg-light" value="<?= $po_number ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="po_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-select" required>
                                <option value=""> Pilih Supplier </option>
                                <?php foreach ($suppliers as $sup): ?>
                                    <option value="<?= $sup['id'] ?>"><?= $sup['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Detail Item -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-muted mb-0">Daftar Barang</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addItem()">
                                <i class="bi bi-plus"></i> Tambah Baris
                            </button>
                        </div>
                        
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Pilih Sumber Barang</th>
                                    <th>Harga Beli</th>
                                    <th>Satuan</th>
                                    <th width="150"> Qty</th>
                                    <th width="80" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="item_list">
                                <!-- Baris Pertama (Default) -->
                                <tr>
                                    <td>
                                        <select name="product_source[]" class="form-select product-source" required>
                                            <option value=""> Pilih Sumber Barang </option>
                                            <option value="manual">Masukan Manual</option>
                                            <optgroup label="Master Barang">
                                            <?php foreach ($items as $item): ?>
                                                <option value="item:<?= $item['id'] ?>" data-name="<?= esc($item['name']) ?>" data-unit="<?= esc($item['unit']) ?>" data-stock="<?= (int) ($item['stock'] ?? 0) ?>"><?= $item['sku'] ?> - <?= $item['name'] ?> (<?= $item['unit'] ?>) - Stok: <?= (int) ($item['stock'] ?? 0) ?></option>
                                            <?php endforeach; ?>
                                            </optgroup>
                                            <optgroup label="Master Bahan Baku">
                                            <?php foreach ($raw_materials as $rawMaterial): ?>
                                                <option value="raw_material:<?= $rawMaterial['id'] ?>" data-name="<?= esc($rawMaterial['name']) ?>" data-unit="<?= esc($rawMaterial['unit']) ?>" data-stock="<?= (int) ($rawMaterial['stock'] ?? 0) ?>"><?= $rawMaterial['sku'] ?> - <?= $rawMaterial['name'] ?> (<?= $rawMaterial['unit'] ?>) - Stok: <?= (int) ($rawMaterial['stock'] ?? 0) ?></option>
                                            <?php endforeach; ?>
                                            </optgroup>
                                        </select>
                                        <input type="hidden" name="product_name[]" class="product-name">
                                        <input type="text" class="form-control product-name-manual mt-2 d-none" placeholder="Tulis nama barang">
                                    </td>
                                    <td><input type="number" name="purchase_price[]" class="form-control purchase-price" required min="0" step="0.01" placeholder="Harga beli"></td>
                                    <td>
                                        <input type="text" name="unit[]" class="form-control unit-input" required placeholder="pcs/box/kg">
                                    </td>
                                    <td><input type="number" name="qty[]" class="form-control" required min="1" placeholder="0"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger disabled"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="text-end mt-4">
                            <a href="<?= base_url('purchases') ?>" class="btn btn-light me-2">Batal</a>
                            <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-1"></i> Simpan PO</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Script untuk Menambah Baris Barang Secara Dinamis -->
    <script>
        function addItem() {
            let sourceOptions = `<option value=""> Pilih Sumber Barang </option><option value="manual">Input Langsung</option><optgroup label="Master Barang">`;
            <?php foreach ($items as $item): ?>
                sourceOptions += `<option value="item:<?= $item['id'] ?>" data-name="<?= esc($item['name']) ?>" data-unit="<?= esc($item['unit']) ?>" data-stock="<?= (int) ($item['stock'] ?? 0) ?>"><?= $item['sku'] ?> - <?= $item['name'] ?> (<?= $item['unit'] ?>) - Stok: <?= (int) ($item['stock'] ?? 0) ?></option>`;
            <?php endforeach; ?>
            sourceOptions += `</optgroup><optgroup label="Master Bahan Baku">`;
            <?php foreach ($raw_materials as $rawMaterial): ?>
                sourceOptions += `<option value="raw_material:<?= $rawMaterial['id'] ?>" data-name="<?= esc($rawMaterial['name']) ?>" data-unit="<?= esc($rawMaterial['unit']) ?>" data-stock="<?= (int) ($rawMaterial['stock'] ?? 0) ?>"><?= $rawMaterial['sku'] ?> - <?= $rawMaterial['name'] ?> (<?= $rawMaterial['unit'] ?>) - Stok: <?= (int) ($rawMaterial['stock'] ?? 0) ?></option>`;
            <?php endforeach; ?>
            sourceOptions += `</optgroup>`;

            let tr = `
                <tr>
                    <td><select name="product_source[]" class="form-select product-source" required>${sourceOptions}</select><input type="hidden" name="product_name[]" class="product-name"><input type="text" class="form-control product-name-manual mt-2 d-none" placeholder="Tulis nama barang"></td>
                    <td><input type="number" name="purchase_price[]" class="form-control purchase-price" required min="0" step="0.01" placeholder="Harga beli"></td>
                    <td>
                        <input type="text" name="unit[]" class="form-control unit-input" required placeholder="pcs/box/kg">
                    </td>
                    <td><input type="number" name="qty[]" class="form-control" required min="1" placeholder="0"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            document.getElementById('item_list').insertAdjacentHTML('beforeend', tr);
        }

        document.querySelector('form').addEventListener('submit', function (event) {
            let valid = true;
            document.querySelectorAll('#item_list tr').forEach(function (row) {
                const sourceSelect = row.querySelector('.product-source');
                const productName = row.querySelector('.product-name');
                const manualName = row.querySelector('.product-name-manual');
                const unitInput = row.querySelector('.unit-input');
                const purchasePrice = row.querySelector('.purchase-price');
                const hasSource = sourceSelect.value !== '';
                const hasProductName = productName.value.trim() !== '';
                const hasUnit = unitInput.value.trim() !== '';
                const hasPurchasePrice = purchasePrice.value !== '' && Number(purchasePrice.value) >= 0;

                sourceSelect.classList.toggle('is-invalid', !hasSource);
                manualName.classList.toggle('is-invalid', !hasProductName);
                unitInput.classList.toggle('is-invalid', !hasUnit);
                purchasePrice.classList.toggle('is-invalid', !hasPurchasePrice);

                if (!hasSource || !hasProductName || !hasUnit || !hasPurchasePrice) {
                    valid = false;
                }
            });

            if (!valid) {
                event.preventDefault();
                alert('Pilih sumber barang, isi nama barang, satuan, dan harga beli pada setiap baris.');
            }
        });

        document.addEventListener('change', function (event) {
            if (event.target.classList.contains('product-source')) {
                const row = event.target.closest('tr');
                const isManual = event.target.value === 'manual';
                const productName = row.querySelector('.product-name');
                const manualName = row.querySelector('.product-name-manual');
                productName.value = isManual ? '' : getSelectedData(event.target, 'name');
                manualName.value = isManual ? '' : productName.value;
                manualName.classList.toggle('d-none', !isManual);
                manualName.required = isManual;
                const unitInput = row.querySelector('.unit-input');
                unitInput.value = isManual ? '' : getSelectedData(event.target, 'unit');
                unitInput.readOnly = !isManual && event.target.value !== '';
            }
        });

        document.addEventListener('input', function (event) {
            if (event.target.classList.contains('product-name-manual')) {
                event.target.closest('tr').querySelector('.product-name').value = event.target.value;
            }
        });

        function getSelectedData(select, key) {
            const selectedOption = select.options[select.selectedIndex];
            return selectedOption.dataset[key] || '';
        }
    </script>
<?= $this->endSection() ?>