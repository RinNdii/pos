<?php
namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $today = new \DateTimeImmutable('today');
        $periodStart = $today->modify('-6 days');
        $startDate = $periodStart->format('Y-m-d');
        $endDate = $today->format('Y-m-d');
        $summaryStartDate = $today->modify('-29 days')->format('Y-m-d');
        $chartKeys = [];
        $purchaseData = [];
        $receivingData = [];
        $saleData = [];

        for ($date = $periodStart; $date <= $today; $date = $date->modify('+1 day')) {
            $key = $date->format('Y-m-d');
            $chartKeys[] = $key;
            $purchaseData[$key] = 0;
            $receivingData[$key] = 0;
            $saleData[$key] = 0;
        }
        
        $data = [
            'username'        => session()->get('username'),
            'role'            => session()->get('role'),
            'total_items'     => $db->table('items')->countAllResults(),
            'total_suppliers' => $db->table('suppliers')->countAllResults(),
            'total_customers' => $db->table('customers')->countAllResults(),
            'total_purchases' => $db->table('purchases')->countAllResults(),
        ];

        $data['recent_sales'] = $db->table('sales')
            ->select('sales.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales.customer_id', 'left')
            ->orderBy('sale_date', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $data['recent_purchases'] = $db->table('purchases')
            ->select('purchases.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchases.supplier_id', 'left')
            ->orderBy('po_date', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $incomeRow = $db->table('sales')
            ->selectSum('total_amount', 'total_income')
            ->where('sale_date >=', $summaryStartDate . ' 00:00:00')
            ->where('sale_date <=', $endDate . ' 23:59:59')
            ->get()->getRowArray();
        $expenseRow = $db->table('purchase_details')
            ->select('SUM(purchase_details.qty * purchase_details.purchase_price) as total_expense')
            ->join('purchases', 'purchases.id = purchase_details.purchase_id')
            ->where('purchases.po_date >=', $summaryStartDate)
            ->where('purchases.po_date <=', $endDate)
            ->get()->getRowArray();
        $data['total_income_week'] = (float) ($incomeRow['total_income'] ?? 0);
        $data['total_expense_week'] = (float) ($expenseRow['total_expense'] ?? 0);

        // Ambil rekap aktivitas harian selama tujuh hari terakhir.
        $purchases = $db->query(
            "SELECT DATE(po_date) as date_key, COUNT(id) as total
             FROM purchases
             WHERE po_date BETWEEN ? AND ?
             GROUP BY DATE(po_date)",
            [$startDate, $endDate]
        )->getResultArray();

        $receivings = $db->query(
            "SELECT DATE(receive_date) as date_key, COUNT(id) as total
             FROM receivings
             WHERE receive_date BETWEEN ? AND ?
             GROUP BY DATE(receive_date)",
            [$startDate, $endDate]
        )->getResultArray();

        $sales = $db->query(
            "SELECT DATE(sale_date) as date_key, COUNT(id) as total
             FROM sales
             WHERE sale_date BETWEEN ? AND ?
             GROUP BY DATE(sale_date)",
            [$startDate . ' 00:00:00', $endDate . ' 23:59:59']
        )->getResultArray();

        foreach ($purchases as $purchase) {
            $purchaseData[$purchase['date_key']] = (int) $purchase['total'];
        }

        foreach ($receivings as $r) {
            $receivingData[$r['date_key']] = (int) $r['total'];
        }

        foreach ($sales as $sale) {
            $saleData[$sale['date_key']] = (int) $sale['total'];
        }

        $data['chart_days'] = array_map(static fn ($key) => date('d M', strtotime($key)), $chartKeys);
        $data['chart_period'] = '7 Hari Terakhir';
        $data['chart_purchases'] = array_values($purchaseData);
        $data['chart_receivings'] = array_values($receivingData);
        $data['chart_sales'] = array_values($saleData);

        return view('dashboard/index', $data);
    }
}