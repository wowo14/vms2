<?php

use yii\db\Migration;

/**
 * Phase 6 Backfill Migration.
 * Populates the new tables from existing minikompetisi_penawaran data.
 * Safe to run on live data — only INSERTs, no modifies to old tables.
 */
class m260312_070001_backfill_pricing_data extends Migration
{
    public function safeUp()
    {
        $db = $this->db;

        // ── Step 1: Backfill quotation_version from minikompetisi_penawaran ──
        $penawaran = (new \yii\db\Query())
            ->select([
                'p.id',
                'p.minikompetisi_id',
                'p.vendor_id',
                'p.created_at',
                'p.total_harga',
                'p.total_skor_kualitas',
                'p.total_skor_harga',
                'p.total_skor_akhir',
                'p.ranking',
                'p.is_winner',
                'm.company_id',
                'm.fiscal_year',
                'm.tanggal',
                'm.location'
            ])
            ->from(['p' => 'minikompetisi_penawaran'])
            ->leftJoin(['m' => 'minikompetisi'], 'm.id = p.minikompetisi_id')
            ->all($db);

        $processedCount = 0;
        foreach ($penawaran as $p) {
            // Check if already backfilled
            $exists = (new \yii\db\Query())
                ->from('quotation_version')
                ->where(['minikompetisi_id' => $p['minikompetisi_id'], 'vendor_id' => $p['vendor_id']])
                ->exists($db);

            if ($exists)
                continue;

            // Insert quotation_version (version 1 = historical)
            $db->createCommand()->insert('quotation_version', [
                'minikompetisi_id' => $p['minikompetisi_id'],
                'vendor_id' => $p['vendor_id'],
                'version_number' => 1,
                'version_label' => 'v1',
                'revision_note' => 'Backfill dari data lama (minikompetisi_penawaran id=' . $p['id'] . ')',
                'status' => 1,
                'is_latest' => 1,
                'uploaded_at' => $p['created_at'],
                'uploaded_by' => null,
                'total_harga' => $p['total_harga'],
                'total_skor_kualitas' => $p['total_skor_kualitas'],
                'total_skor_harga' => $p['total_skor_harga'],
                'total_skor_akhir' => $p['total_skor_akhir'],
                'ranking' => $p['ranking'],
                'is_winner' => $p['is_winner'],
            ])->execute();

            $qvId = $db->getLastInsertID();

            // ── Step 2: Backfill quotation_item from minikompetisi_penawaran_item ──
            $pItems = (new \yii\db\Query())
                ->select([
                    'pi.id',
                    'pi.item_id',
                    'pi.harga_penawaran',
                    'pi.skor_kualitas',
                    'pi.keterangan',
                    'mi.nama_produk',
                    'mi.qty',
                    'mi.satuan',
                    'mi.harga_hps',
                    'mi.product_category',
                    'mi.specification'
                ])
                ->from(['pi' => 'minikompetisi_penawaran_item'])
                ->leftJoin(['mi' => 'minikompetisi_item'], 'mi.id = pi.item_id')
                ->where(['pi.penawaran_id' => $p['id']])
                ->all($db);

            foreach ($pItems as $pi) {
                $normName = $this->simplifyName($pi['nama_produk'] ?? '');
                $totalPrice = (float) ($pi['harga_penawaran'] ?? 0) * (float) ($pi['qty'] ?? 1);
                $hpsPrice = (float) ($pi['harga_hps'] ?? 0);
                $priceRatio = ($hpsPrice > 0 && $pi['harga_penawaran'] > 0)
                    ? round($pi['harga_penawaran'] / $hpsPrice, 4)
                    : null;

                // quotation_item
                $db->createCommand()->insert('quotation_item', [
                    'quotation_version_id' => $qvId,
                    'minikompetisi_item_id' => $pi['item_id'],
                    'product_catalog_id' => null,
                    'product_name' => $pi['nama_produk'] ?? '',
                    'product_name_norm' => $normName,
                    'product_category' => $pi['product_category'],
                    'specification' => $pi['specification'],
                    'unit' => $pi['satuan'],
                    'quantity' => $pi['qty'] ?? 1,
                    'unit_price' => $pi['harga_penawaran'] ?? 0,
                    'total_price' => $totalPrice,
                    'skor_kualitas' => $pi['skor_kualitas'],
                    'keterangan' => $pi['keterangan'],
                ])->execute();

                // pricing_dataset
                $procDate = $p['tanggal'] ?? date('Y-m-d');
                $fiscalYear = $p['fiscal_year'] ?? (int) date('Y', strtotime($procDate));

                $db->createCommand()->insert('pricing_dataset', [
                    'company_id' => $p['company_id'] ?? 1,
                    'fiscal_year' => $fiscalYear,
                    'procurement_date' => $procDate,
                    'procurement_id' => $p['minikompetisi_id'],
                    'quotation_version_id' => $qvId,
                    'vendor_id' => $p['vendor_id'],
                    'product_catalog_id' => null,
                    'product_raw_name' => $pi['nama_produk'] ?? '',
                    'product_norm_name' => $normName,
                    'product_category' => $pi['product_category'],
                    'specification' => $pi['specification'],
                    'location' => $p['location'],
                    'unit' => $pi['satuan'],
                    'quantity' => $pi['qty'] ?? 1,
                    'unit_price' => $pi['harga_penawaran'] ?? 0,
                    'total_price' => $totalPrice,
                    'hps_price' => $hpsPrice ?: null,
                    'price_ratio_vs_hps' => $priceRatio,
                    'is_winner' => $p['is_winner'],
                    'version_number' => 1,
                    'is_latest_version' => 1,
                    'ingested_at' => date('Y-m-d H:i:s'),
                ])->execute();
            }

            $processedCount++;
        }

        echo "Backfilled $processedCount penawaran records.\n";
    }

    public function safeDown()
    {
        // Remove only backfill data (safe because we can re-run)
        $this->delete('pricing_dataset', ['version_number' => 1]);
        // Note: quotation_version and quotation_item not deleted to avoid FK issues
        // Manual migration down if needed
    }

    /**
     * Simple name normalization (no service available in migration context).
     */
    private function simplifyName(string $name): string
    {
        $n = mb_strtolower(trim($name));
        $n = preg_replace('/[^a-z0-9\s]/', ' ', $n);
        $n = preg_replace('/\s+/', ' ', $n);
        $words = array_filter(explode(' ', trim($n)), fn($w) => strlen($w) > 1);
        sort($words);
        return implode(' ', $words);
    }
}
