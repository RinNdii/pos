<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
    <div class="mb-4">
        <h2 class="fw-bold" style="color: #2d3748;">Dashboard</h2>
        <p class="text-muted">Selamat datang kembali, <strong><?= ucfirst($username) ?></strong>! Anda login sebagai <span class="badge bg-info text-dark"><?= strtoupper($role) ?></span>.</p>
    </div>

    <div class="row g-4">
        <!-- Kartu Total Barang (Semua Role) -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-white p-3 shadow-sm d-flex justify-content-center align-items-center me-4">
                        <i class="bi bi-box-seam fs-2" style="color: #00bcd4;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-bold text-uppercase">Total Barang</h6>
                        <h2 class="mb-0 fw-bold" style="color: #00838f;"><?= $total_items ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Total Customer (Admin & Kasir) -->
        <?php if(in_array($role, ['admin', 'kasir'])): ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-white p-3 shadow-sm d-flex justify-content-center align-items-center me-4">
                        <i class="bi bi-people fs-2" style="color: #9c27b0;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-bold text-uppercase">Total Customer</h6>
                        <h2 class="mb-0 fw-bold" style="color: #6a1b9a;"><?= $total_customers ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Kartu Total Supplier (Khusus Admin) -->
        <?php if($role === 'admin'): ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="rounded-circle bg-white p-3 shadow-sm d-flex justify-content-center align-items-center me-4">
                        <i class="bi bi-cart-check fs-2" style="color: #3f51b5;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-bold text-uppercase">Data Pembelian</h6>
                        <h2 class="mb-0 fw-bold" style="color: #283593;"><?= $total_purchases ?? 0 ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Area Grafik (Chart) -->
    <div class="row g-4 mt-4 mb-4 align-items-stretch">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold text-uppercase" style="color: #283593;"><i class="bi bi-graph-up-arrow me-2"></i>Grafik Aktivitas Transaksi - <?= esc($chart_period ?? date('F Y')) ?></h6>
                </div>
                <div class="card-body">
                    <canvas id="transactionChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4 h-100">
                <div class="card border-0 shadow-sm rounded-4 flex-fill" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fw-bold text-uppercase">Total Pemasukan</h6>
                            <h3 class="mb-0 fw-bold text-success">Rp <?= number_format($total_income_week ?? 0, 0, ',', '.') ?></h3>
                            <small class="text-muted">30 hari terakhir</small>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm rounded-4 flex-fill" style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fw-bold text-uppercase">Total Pengeluaran</h6>
                            <h3 class="mb-0 fw-bold text-danger">Rp <?= number_format($total_expense_week ?? 0, 0, ',', '.') ?></h3>
                            <small class="text-muted">30 hari terakhir</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Area Tabel Transaksi Terbaru -->
    <div class="row g-4">
        <!-- Tabel Penjualan (Kiri) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-uppercase mb-0" style="color: #283593;"><i class="bi bi-cart-check me-2"></i>Penjualan Terbaru</h6>
                    <a href="<?= base_url('pos/history') ?>" class="text-decoration-none text-muted small">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover align-middle">
                            <tbody>
                                <?php if(empty($recent_sales)): ?>
                                    <tr><td class="text-center text-muted">Belum ada transaksi.</td></tr>
                                <?php else: ?>
                                    <?php foreach($recent_sales as $sale): ?>
                                    <tr class="border-bottom">
                                        <td>
                                            <div class="fw-bold"><?= $sale['invoice_number'] ?></div>
                                            <small class="text-muted"><?= date('d M Y', strtotime($sale['sale_date'])) ?></small>
                                        </td>
                                        <td><?= $sale['customer_name'] ?? 'Umum' ?></td>
                                        <td class="text-end fw-bold text-success">Rp <?= number_format($sale['total_amount'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Pembelian/PO (Kanan) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-uppercase mb-0" style="color: #283593;"><i class="bi bi-box-arrow-in-down me-2"></i>PO / Pembelian Terbaru</h6>
                    <a href="<?= base_url('purchases') ?>" class="text-decoration-none text-muted small">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover align-middle">
                            <tbody>
                                <?php if(empty($recent_purchases)): ?>
                                    <tr><td class="text-center text-muted">Belum ada pembelian.</td></tr>
                                <?php else: ?>
                                    <?php foreach($recent_purchases as $po): ?>
                                    <tr class="border-bottom">
                                        <td>
                                            <div class="fw-bold"><?= $po['po_number'] ?></div>
                                            <small class="text-muted"><?= date('d M Y', strtotime($po['po_date'])) ?></small>
                                        </td>
                                        <td><?= $po['supplier_name'] ?></td>
                                        <td class="text-end">
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">
                                                <?= $po['status'] === 'Pending' ? 'Dipesan' : esc($po['status']) ?>
                                            </span>
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
    </div>

    <!-- CDN dan Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('transactionChart').getContext('2d');
            
            // Menggunakan data fallback agar grafik tetap muncul meski variabel controller kosong
            const labels = <?= json_encode($chart_days ?? []) ?>;
            const dataPurchases = <?= json_encode($chart_purchases ?? array_fill(0, 7, 0)) ?>;
            const dataReceivings = <?= json_encode($chart_receivings ?? array_fill(0, 7, 0)) ?>;
            const dataSales = <?= json_encode($chart_sales ?? array_fill(0, 7, 0)) ?>;

            new Chart(ctx, {
                type: 'line', 
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pembelian',
                            data: dataPurchases,
                            borderColor: '#3f51b5',
                            backgroundColor: 'rgba(63, 81, 181, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Penerimaan',
                            data: dataReceivings,
                            borderColor: '#00bcd4',
                            backgroundColor: 'rgba(0, 188, 212, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Penjualan',
                            data: dataSales,
                            borderColor: '#e67e22',
                            backgroundColor: 'rgba(230, 126, 34, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        });
    </script>
    
<?= $this->endSection() ?>