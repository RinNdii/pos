<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem POS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f7f6; /* Soft blue/grey background */
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        /* Styling Sidebar dengan palet Navy */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 240px;
            height: 100vh;
            flex: 0 0 240px;
            background-color: #1a2530;
            color: #fff;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            overflow: hidden;
        }
        .sidebar .nav-link {
            color: #a0aec0;
            border-radius: 8px;
            margin-bottom: 2px;
            padding: 7px 12px;
            font-size: 0.86rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(77, 208, 225, 0.15); /* Soft cyan hover */
            color: #4dd0e1;
        }
        .sidebar .menu-header {
            font-size: 0.65rem;
            letter-spacing: 1px;
            color: #4a5568;
            margin: 10px 0 3px 12px;
            text-transform: uppercase;
            font-weight: 700;
        }
        .content-area {
            flex-grow: 1;
            margin-left: 240px;
            padding: 24px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-2">
        <a href="/" class="d-flex align-items-center mb-2 text-white text-decoration-none">
            <i class="bi bi-box-seam fs-4 me-2" style="color: #4dd0e1;"></i>
            <span class="fs-5 fw-bold">POS System</span>
        </a>
        <hr class="text-secondary mt-0">
        
        <ul class="nav nav-pills flex-column mb-auto">
            <!-- Tampil untuk semua role -->
            <li class="nav-item">
                <a href="<?= base_url('dashboard') ?>" class="nav-link"><i class="bi bi-grid me-2"></i> Dashboard</a>
            </li>

            <!-- MENU KHUSUS ADMIN -->
            <?php if(session()->get('role') === 'admin'): ?>
                <div class="menu-header">Master Data</div>
                <li>
                    <a href="<?= base_url('items') ?>" class="nav-link"><i class="bi bi-tags me-2"></i> Produk</a>
                </li>
                <li> 
                    <a href="<?= base_url('raw_materials') ?>" class="nav-link"> <i class="bi bi-box-seam me-2"></i> Bahan Baku </a>
                </li>
                <li>
                    <a href="<?= base_url('suppliers') ?>" class="nav-link"><i class="bi bi-truck me-2"></i> Supplier</a>
                </li>
                <li>
                    <a href="<?= base_url('customers') ?>" class="nav-link"><i class="bi bi-people me-2"></i> Customer</a>
                </li>
                
                <div class="menu-header mt-3">Pengadaan & Gudang</div>
                <li>
                    <a href="<?= base_url('purchases') ?>" class="nav-link"><i class="bi bi-cart-plus me-2"></i> Pembelian (PO)</a>
                </li>
                <li>
                    <a href="<?= base_url('receivings') ?>" class="nav-link"><i class="bi bi-box-arrow-in-down me-2"></i> Penerimaan</a>
                </li>
                <li>
                    <a href="<?= base_url('warehouse') ?>" class="nav-link"><i class="bi bi-box-seam me-2"></i> Gudang</a>
                </li>
                <div class="menu-header mt-3">Penjualan 
                </div>
                <a href="<?= base_url('pos') ?>" class="nav-link <?= uri_string() == 'pos' ? 'text-white fw-bold' : '' ?>">
            <i class="bi bi-calculator me-2"></i> Point of Sales
        </a>
                <a href="<?= base_url('pos/history') ?>" class="nav-link" ><i class="bi bi-clock-history me-2"></i> Riwayat Penjualan</a>
    </a>
            <?php endif; ?>

            <?php if(session()->get('role') === 'gudang'): ?>
                <div class="menu-header">Gudang</div>
                <li>
                    <a href="<?= base_url('warehouse') ?>" class="nav-link"><i class="bi bi-box-seam me-2"></i> Gudang</a>
                </li>
                <li>
                    <a href="<?= base_url('raw_materials') ?>" class="nav-link"><i class="bi bi-boxes me-2"></i> Bahan Baku</a>
                </li>
                <div class="menu-header mt-3">Penerimaan</div>
                <li>
                    <a href="<?= base_url('receivings') ?>" class="nav-link"><i class="bi bi-box-arrow-in-down me-2"></i> Penerimaan</a>
                </li>
            <?php endif; ?>

            <!-- Menu Kasir (Hanya tampil untuk Kasir) -->

<!-- Menu Khusus Kasir -->
<?php if(session()->get('role') === 'kasir'): ?>
    <div class="menu-header">Menu Kasir</div>
    <li>
        <a href="<?= base_url('pos') ?>" class="nav-link <?= uri_string() == 'pos' ? 'text-white fw-bold' : '' ?>">
            <i class="bi bi-calculator me-2"></i> Penjualan (POS)
        </a>
    </li>
    <a href="<?= base_url('pos/history') ?>" class="nav-link <?= uri_string() == 'pos/history' ? 'text-white fw-bold' : '' ?>">
        <i class="bi bi-clock-history me-2"></i> Riwayat
    </a>
    <li>
        <a href="<?= base_url('customers') ?>" class="nav-link <?= uri_string() == 'customers' ? 'text-white fw-bold' : '' ?>">
            <i class="bi bi-people me-2"></i> Master Customer
        </a>
    </li>
    
<?php endif; ?>
        </ul>
        
        <hr class="text-secondary">
        <div class="mt-auto">
            <a href="<?= base_url('auth/logout') ?>" class="nav-link text-danger fw-bold text-decoration-none p-2 rounded" style="background: rgba(220,53,69,0.1);">
                <i class="bi bi-box-arrow-right me-2"></i> Logout (<?= session()->get('username') ?>)
            </a>
        </div>
    </div>

    <!-- Area Konten Utama -->
    <div class="content-area">
        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>