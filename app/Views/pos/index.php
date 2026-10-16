<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .pos-page { color: #223047; }
    .pos-toolbar, .pos-cart { background: #fff; border: 1px solid #e4ebf5; border-radius: 14px; }
    .pos-toolbar { padding: 14px 16px; }
    .pos-cart { position: sticky; top: 20px; overflow: hidden; }
    .pos-cart-items { max-height: 44vh; overflow-y: auto; }
    .pos-cart-items::-webkit-scrollbar { width: 5px; }
    .pos-cart-items::-webkit-scrollbar-thumb { background: #cbd7e8; border-radius: 8px; }
    .product-card { border: 1px solid #e4ebf5; border-radius: 14px; cursor: pointer; transition: .2s ease; }
    .product-card:hover { border-color: #2f6fed; box-shadow: 0 8px 20px rgba(47, 111, 237, .12); transform: translateY(-2px); }
    .product-image { width: 100%; height: 150px; object-fit: cover; border-radius: 10px; background: #f1f6fd; }
    .category-chip { border: 1px solid #dfe7f2; color: #52637c; background: #fff; border-radius: 8px; padding: 7px 14px; font-size: .8rem; }
    .category-chip.active, .category-chip:hover { color: #fff; background: #2f6fed; border-color: #2f6fed; }
    .cart-line { border-bottom: 1px solid #edf1f7; padding: 12px 0; }
    .cart-line:last-child { border-bottom: 0; }
    .cart-thumb { width: 42px; height: 42px; display: grid; place-items: center; color: #2f6fed; background: #f1f6fd; border-radius: 8px; }
    .qty-control { display: inline-flex; align-items: center; gap: 7px; }
    .qty-control button { width: 23px; height: 23px; border: 1px solid #dce5f1; background: #fff; border-radius: 6px; line-height: 1; color: #2f6fed; }
    .payment-option { border: 1px solid #dfe7f2; border-radius: 8px; padding: 9px 5px; text-align: center; color: #52637c; font-size: .75rem; cursor: pointer; }
    .payment-option.active { border-color: #2f6fed; color: #2f6fed; background: #f1f6fd; }
    .payment-option input { display: none; }
    .qris-preview { width: min(260px, 100%); border: 1px solid #e4ebf5; border-radius: 12px; }
    .transfer-bank { display: none; }
    .transfer-bank.visible { display: block; }
    @media (max-width: 991.98px) { .pos-cart { position: static; } }
</style>

<div class="pos-page">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 shadow-sm"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-cart3 text-primary me-2"></i>Point of Sale</h3>
            <span class="text-muted small">Pilih produk untuk ditambahkan ke keranjang</span>
        </div>
        <div class="text-end text-muted small">
            <div class="fw-semibold"><?= date('d F Y') ?></div>
            <div id="realtime-clock"></div>
        </div>
    </div>

    <div class="row g-3 align-items-start">
        <div class="col-xl-8">
            <div class="pos-toolbar mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="search" id="product-search" class="form-control border-start-0" placeholder="Cari produk...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select id="sort-products" class="form-select">
                            <option value="default">Urutan: Terbaru</option>
                            <option value="name">Nama A-Z</option>
                            <option value="price-low">Harga Terendah</option>
                            <option value="price-high">Harga Tertinggi</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3" id="category-filter">
                    <button type="button" class="category-chip active" data-filter="all">Semua</button>
                    <button type="button" class="category-chip" data-filter="Pcs">Pcs</button>
                    <button type="button" class="category-chip" data-filter="Box">Box</button>
                    <button type="button" class="category-chip" data-filter="Kg">Kg</button>
                    <button type="button" class="category-chip" data-filter="Pack">Pack</button>
                    <button type="button" class="category-chip" data-filter="Custom">Custom</button>
                </div>
            </div>

            <div class="row g-3" id="product-grid">
                <?php foreach ($items as $item): ?>
                    <div class="col-md-4 col-sm-6 product-wrapper" data-name="<?= esc(strtolower($item['name'])) ?>" data-unit="<?= esc($item['unit']) ?>" data-price="<?= esc($item['selling_price']) ?>">
                        <div class="card h-100 product-card" onclick="addFromCard(this)"
                             data-id="<?= esc($item['id']) ?>"
                             data-name="<?= esc($item['name']) ?>"
                             data-price="<?= esc($item['selling_price']) ?>"
                             data-stock="<?= esc($item['stock']) ?>">
                            <div class="card-body p-3">
                                <img class="product-image mb-3" src="<?= !empty($item['image']) ? base_url($item['image']) : base_url('uploads/products/product-placeholder.svg') ?>" alt="<?= esc($item['name']) ?>" loading="lazy">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <h6 class="fw-bold mb-1 text-truncate" title="<?= esc($item['name']) ?>"><?= esc($item['name']) ?></h6>
                                    <button type="button" class="btn btn-primary btn-sm rounded-circle p-0 flex-shrink-0" style="width: 26px;height:26px" onclick="event.stopPropagation(); addFromCard(this.closest('.product-card'))"><i class="bi bi-plus"></i></button>
                                </div>
                                <span class="badge bg-light text-primary border mb-2"><?= esc($item['unit']) ?></span>
                                <div class="small text-muted mb-2">Stok: <strong><?= esc($item['stock']) ?></strong></div>
                                <div class="fw-bold text-primary">Rp <?= number_format($item['selling_price'], 0, ',', '.') ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="no-products" class="text-center text-muted py-5 d-none">Produk tidak ditemukan.</div>
        </div>

        <div class="col-xl-4">
            <div class="pos-cart shadow-sm">
                <form action="<?= base_url('pos/checkout') ?>" method="post" id="form-checkout">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div><h5 class="fw-bold mb-1">Keranjang Pesanan</h5><span class="small text-muted"><span id="cart-count">0</span> item</span></div>
                        <button type="button" class="btn btn-light text-danger" onclick="clearCart()" title="Kosongkan keranjang"><i class="bi bi-trash"></i></button>
                    </div>
                    <div class="p-3">
                        <select name="customer_id" class="form-select mb-3">
                            <option value="">Custommer</option>
                            <?php foreach ($customers as $customer): ?><option value="<?= esc($customer['id']) ?>"><?= esc($customer['name']) ?></option><?php endforeach; ?>
                        </select>
                        <div id="cart-items" class="pos-cart-items">
                            <div id="empty-cart-msg" class="text-center text-muted py-5"><i class="bi bi-cart-x fs-1 d-block mb-2 text-light"></i>Belum ada produk</div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Subtotal</span><strong id="cart-subtotal">Rp 0</strong></div>
                        <div class="d-flex justify-content-between align-items-center small mb-3"><span class="text-muted">Diskon</span><div class="input-group input-group-sm w-50"><input type="number" name="discount" id="cart-discount" class="form-control text-end" value="0" min="0" max="100" step="0.01" oninput="calculateTotal()"><span class="input-group-text bg-white">%</span></div></div>
                        <div class="d-flex justify-content-between align-items-center bg-light rounded-3 p-3 mb-3"><strong>Total</strong><strong class="text-primary fs-5" id="cart-total">Rp 0</strong></div>
                        <div class="small text-muted mb-2">Metode Pembayaran</div>
                        <div class="row g-2 mb-3">
                            <div class="col-4"><label class="payment-option active w-100"><input type="radio" name="payment_method" value="Tunai" checked><i class="bi bi-cash d-block fs-5"></i>Tunai</label></div>
                            <div class="col-4"><label class="payment-option w-100"><input type="radio" name="payment_method" value="QRIS"><i class="bi bi-qr-code d-block fs-5"></i>QRIS</label></div>
                            <div class="col-4"><label class="payment-option w-100"><input type="radio" name="payment_method" value="Transfer"><i class="bi bi-bank d-block fs-5"></i>Transfer</label></div>
                        </div>
                        <div class="transfer-bank mb-3" id="transfer-bank-wrap">
                            <label for="transfer-bank" class="form-label small text-muted mb-1">Pilih bank transfer</label>
                            <select name="transfer_bank" id="transfer-bank" class="form-select">
                                <option value="">Pilih bank...</option>
                                <option>BRI (Bank Rakyat Indonesia)</option>
                                <option>Bank Mandiri</option>
                                <option>BNI (Bank Negara Indonesia)</option>
                                <option>BTN (Bank Tabungan Negara)</option>
                                <option>BCA (Bank Central Asia)</option>
                                <option>CIMB Niaga</option>
                                <option>Bank Danamon</option>
                                <option>PermataBank</option>
                                <option>Panin Bank</option>
                                <option>Maybank Indonesia</option>
                                <option>BSI (Bank Syariah Indonesia)</option>
                                <option>Bank Muamalat</option>
                                <option>BCA Syariah</option>
                            </select>
                        </div>
                        <input type="hidden" name="invoice_number" value="<?= esc($invoice_number) ?>">
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" id="btn-pay" disabled><i class="bi bi-credit-card me-2"></i>Bayar Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="qrisModalLabel"><i class="bi bi-qr-code me-2 text-primary"></i>Pembayaran QRIS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted small mb-2">Silakan scan QRIS demo berikut untuk simulasi pembayaran.</p>
                <img src="<?= base_url('uploads/qris-demo.svg') ?>" class="qris-preview mb-3" alt="QRIS demo">
                <div class="small text-muted">Total yang harus dibayar</div>
                <div class="fs-4 fw-bold text-primary" id="qris-modal-total">Rp 0</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="qris-done"><i class="bi bi-check2-circle me-2"></i>Selesai</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="transferModalLabel"><i class="bi bi-bank me-2 text-primary"></i>Pembayaran Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Silakan lakukan transfer ke rekening demo berikut.</p>
                <div class="bg-light rounded-3 p-3 mb-3">
                    <div class="small text-muted">Bank tujuan</div>
                    <div class="fw-bold" id="transfer-modal-bank">-</div>
                    <div class="small text-muted mt-3">Nomor rekening</div>
                    <div class="fs-4 fw-bold text-primary">1122334455</div>
                </div>
                <div class="small text-muted">Total yang harus dibayar</div>
                <div class="fs-4 fw-bold text-primary" id="transfer-modal-total">Rp 0</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="transfer-done"><i class="bi bi-check2-circle me-2"></i>Pembayaran Selesai</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let activeUnit = 'all';
    let qrisConfirmed = false;
    let transferConfirmed = false;
    const formatRupiah = number => new Intl.NumberFormat('id-ID').format(number);

    setInterval(() => { document.getElementById('realtime-clock').textContent = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}); }, 1000);

    function addFromCard(element) {
        addToCart(element.dataset.id, element.dataset.name, Number(element.dataset.price), Number(element.dataset.stock));
    }

    function addToCart(id, name, price, maxStock) {
        const item = cart.find(row => row.id === id);
        if (item) {
            if (item.qty >= item.maxStock) return alert('Stok tidak mencukupi!');
            item.qty++;
        } else {
            if (maxStock < 1) return alert('Stok barang kosong!');
            cart.push({id, name, price, qty: 1, maxStock});
        }
        renderCart();
    }

    function updateQty(id, change) {
        const item = cart.find(row => row.id === id);
        if (!item) return;
        const nextQty = item.qty + change;
        if (nextQty > item.maxStock) return alert('Melebihi stok yang ada!');
        if (nextQty < 1) cart = cart.filter(row => row.id !== id); else item.qty = nextQty;
        renderCart();
    }

    function removeFromCart(id) { cart = cart.filter(row => row.id !== id); renderCart(); }
    function clearCart() { if (cart.length && confirm('Yakin ingin mengosongkan keranjang?')) { cart = []; renderCart(); } }

    function renderCart() {
        const container = document.getElementById('cart-items');
        const empty = document.getElementById('empty-cart-msg');
        container.innerHTML = '';
        document.getElementById('cart-count').textContent = cart.reduce((sum, item) => sum + item.qty, 0);
        document.getElementById('btn-pay').disabled = cart.length === 0;
        if (!cart.length) { container.appendChild(empty); calculateTotal(); return; }
        cart.forEach(item => {
            container.insertAdjacentHTML('beforeend', `<div class="cart-line d-flex align-items-center gap-2">
                <div class="cart-thumb"><i class="bi bi-box"></i></div>
                <div class="flex-grow-1 overflow-hidden"><div class="fw-semibold small text-truncate">${item.name}</div><div class="text-muted" style="font-size:.7rem">Rp ${formatRupiah(item.price)} / pcs</div>
                    <div class="qty-control mt-1"><button type="button" onclick="updateQty('${item.id}', -1)">-</button><span class="small fw-bold">${item.qty}</span><button type="button" onclick="updateQty('${item.id}', 1)">+</button></div>
                </div>
                <div class="text-end"><button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeFromCart('${item.id}')"><i class="bi bi-x"></i></button><div class="fw-bold small">Rp ${formatRupiah(item.price * item.qty)}</div></div>
                <input type="hidden" name="item_id[]" value="${item.id}"><input type="hidden" name="qty[]" value="${item.qty}"><input type="hidden" name="price[]" value="${item.price}">
            </div>`);
        });
        calculateTotal();
    }

    function calculateTotal() {
        const subtotal = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        const discountPercent = Math.min(100, Math.max(0, Number(document.getElementById('cart-discount').value) || 0));
        const discount = subtotal * discountPercent / 100;
        document.getElementById('cart-subtotal').textContent = 'Rp ' + formatRupiah(subtotal);
        const total = Math.max(0, subtotal - discount);
        document.getElementById('cart-total').textContent = 'Rp ' + formatRupiah(total);
        document.getElementById('qris-modal-total').textContent = 'Rp ' + formatRupiah(total);
        document.getElementById('transfer-modal-total').textContent = 'Rp ' + formatRupiah(total);
    }

    function filterProducts() {
        const search = document.getElementById('product-search').value.toLowerCase();
        let visible = 0;
        document.querySelectorAll('.product-wrapper').forEach(card => {
            const show = card.dataset.name.includes(search) && (activeUnit === 'all' || card.dataset.unit.toLowerCase() === activeUnit.toLowerCase());
            card.classList.toggle('d-none', !show); if (show) visible++;
        });
        document.getElementById('no-products').classList.toggle('d-none', visible > 0);
    }

    document.getElementById('product-search').addEventListener('input', filterProducts);
    document.querySelectorAll('.category-chip').forEach(button => button.addEventListener('click', () => {
        document.querySelectorAll('.category-chip').forEach(chip => chip.classList.remove('active'));
        button.classList.add('active'); activeUnit = button.dataset.filter; filterProducts();
    }));
    document.getElementById('sort-products').addEventListener('change', event => {
        const grid = document.getElementById('product-grid');
        [...grid.querySelectorAll('.product-wrapper')].sort((a, b) => {
            if (event.target.value === 'name') return a.dataset.name.localeCompare(b.dataset.name);
            if (event.target.value === 'price-low') return Number(a.dataset.price) - Number(b.dataset.price);
            if (event.target.value === 'price-high') return Number(b.dataset.price) - Number(a.dataset.price);
            return 0;
        }).forEach(node => grid.appendChild(node));
    });
    document.querySelectorAll('.payment-option').forEach(option => option.addEventListener('click', () => {
        document.querySelectorAll('.payment-option').forEach(item => item.classList.remove('active')); option.classList.add('active');
        qrisConfirmed = false;
        transferConfirmed = false;
        const paymentMethod = option.querySelector('input').value;
        document.getElementById('transfer-bank-wrap').classList.toggle('visible', paymentMethod === 'Transfer');
        document.getElementById('transfer-bank').required = paymentMethod === 'Transfer';
    }));
    document.getElementById('form-checkout').addEventListener('submit', event => {
        if (!cart.length) { event.preventDefault(); return; }
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        if (paymentMethod === 'Transfer' && !document.getElementById('transfer-bank').value) {
            event.preventDefault();
            document.getElementById('transfer-bank').focus();
            return;
        }
        if (paymentMethod === 'Transfer' && !transferConfirmed) {
            event.preventDefault();
            calculateTotal();
            document.getElementById('transfer-modal-bank').textContent = document.getElementById('transfer-bank').value;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('transferModal')).show();
            return;
        }
        if (paymentMethod === 'QRIS' && !qrisConfirmed) {
            event.preventDefault();
            calculateTotal();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('qrisModal')).show();
            return;
        }
        const button = document.getElementById('btn-pay'); button.disabled = true; button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
    });
    document.getElementById('qris-done').addEventListener('click', () => {
        qrisConfirmed = true;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('qrisModal')).hide();
        document.getElementById('form-checkout').requestSubmit();
    });
    document.getElementById('transfer-done').addEventListener('click', () => {
        transferConfirmed = true;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('transferModal')).hide();
        document.getElementById('form-checkout').requestSubmit();
    });
</script>
<?= $this->endSection() ?>
