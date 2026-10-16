<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillManualPurchaseItems extends Migration
{
    public function up()
    {
        $manualDetails = $this->db->table('purchase_details')
            ->where('item_id IS NULL', null, false)
            ->where('raw_material_id IS NULL', null, false)
            ->where('product_name IS NOT NULL', null, false)
            ->get()->getResultArray();

        foreach ($manualDetails as $detail) {
            $item = $this->db->table('items')
                ->where('name', $detail['product_name'])
                ->where('unit', $detail['unit'])
                ->get()->getRowArray();

            if (!$item) {
                $this->db->table('items')->insert([
                    'sku'            => 'MAN-' . strtoupper(substr(md5($detail['product_name'] . '|' . $detail['unit']), 0, 8)),
                    'name'           => $detail['product_name'],
                    'unit'           => $detail['unit'],
                    'purchase_price' => $detail['purchase_price'],
                    'selling_price'  => $detail['purchase_price'],
                    'stock'          => 0,
                ]);
                $item = $this->db->table('items')
                    ->where('name', $detail['product_name'])
                    ->where('unit', $detail['unit'])
                    ->get()->getRowArray();
            }

            $this->db->table('purchase_details')
                ->where('id', $detail['id'])
                ->update(['item_id' => $item['id']]);

            $purchase = $this->db->table('purchases')
                ->where('id', $detail['purchase_id'])
                ->get()->getRowArray();
            if ($purchase && $purchase['status'] === 'Diterima') {
                $this->db->table('items')
                    ->where('id', $item['id'])
                    ->set('stock', 'stock + ' . (int) $detail['qty'], false)
                    ->update();
            }
        }
    }

    public function down()
    {
        // Data hasil backfill dipertahankan agar relasi penerimaan tidak rusak.
    }
}