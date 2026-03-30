<?php

namespace app\services;

use app\models\PricingDataset;
use app\models\QuotationVersion;
use app\models\QuotationItem;
use app\models\Minikompetisi;
use app\models\PriceAnalyticsSummary;
use Yii;

/**
 * PricingDatasetService — ingests quotation version data into the analytics dataset.
 */
class PricingDatasetService
{
    /**
     * Ingest all items from a QuotationVersion into pricing_dataset.
     * Marks previous entries for same procurement+vendor as is_latest_version=0.
     *
     * @return int Number of rows ingested
     */
    public function ingest(QuotationVersion $version): int
    {
        $mk = Minikompetisi::findOne($version->minikompetisi_id);
        if (!$mk)
            return 0;

        // Mark old dataset rows for this procurement + vendor as not latest
        Yii::$app->db->createCommand(
            'UPDATE {{%pricing_dataset}} SET is_latest_version = 0
             WHERE procurement_id = :pid AND vendor_id = :vid'
        )->bindValues([
                    ':pid' => $version->minikompetisi_id,
                    ':vid' => $version->vendor_id,
                ])->execute();

        $ingested = 0;
        $items = QuotationItem::find()
            ->with('minikompetisiItem')
            ->where(['quotation_version_id' => $version->id])
            ->all();

        foreach ($items as $qi) {
            $mItem = $qi->minikompetisiItem;
            if (!$mItem)
                continue;

            $hpsPrice = (float) ($mItem->harga_hps ?? 0);
            $ratio = ($hpsPrice > 0 && $qi->unit_price > 0)
                ? round($qi->unit_price / $hpsPrice, 4)
                : null;

            $row = new PricingDataset();
            $row->company_id = $mk->company_id ?? 1;
            $row->fiscal_year = $mk->fiscal_year ?? (int) date('Y', strtotime($mk->tanggal ?? 'now'));
            $row->procurement_date = $mk->tanggal ?? date('Y-m-d');
            $row->procurement_id = $mk->id;
            $row->quotation_version_id = $version->id;
            $row->vendor_id = $version->vendor_id;
            $row->product_catalog_id = $qi->product_catalog_id;
            $row->product_raw_name = $qi->product_name;
            $row->product_norm_name = $qi->product_name_norm;
            $row->product_category = $qi->product_category;
            $row->specification = $qi->specification;
            $row->location = $mk->location ?? null;
            $row->unit = $qi->unit;
            $row->quantity = $qi->quantity;
            $row->unit_price = $qi->unit_price;
            $row->total_price = $qi->unit_price * $qi->quantity;
            $row->hps_price = $hpsPrice ?: null;
            $row->price_ratio_vs_hps = $ratio;
            $row->is_winner = $version->is_winner;
            $row->version_number = $version->version_number;
            $row->is_latest_version = 1;
            $row->ingested_at = date('Y-m-d H:i:s');

            if ($row->save(false)) {
                $ingested++;
            }

            // Refresh summary for this product
            $this->refreshSummary($row->company_id, $qi->product_name_norm ?? '');
        }

        return $ingested;
    }

    /**
     * Refresh price_analytics_summary for one product + company.
     */
    public function refreshSummary(int $companyId, string $productNormName): void
    {
        if (empty($productNormName))
            return;

        $agg = (new \yii\db\Query())
            ->select([
                'COUNT(*) AS sample_count',
                'COUNT(DISTINCT vendor_id) AS vendor_count',
                'MIN(unit_price) AS min_price',
                'MAX(unit_price) AS max_price',
                'ROUND(AVG(unit_price), 2) AS avg_price',
                'MAX(procurement_date) AS last_seen_at',
                'MIN(product_catalog_id) AS product_catalog_id',
                'MIN(product_category) AS product_category',
                'MIN(fiscal_year) AS fiscal_year',
            ])
            ->from('pricing_dataset')
            ->where(['company_id' => $companyId, 'product_norm_name' => $productNormName])
            ->one();

        if (!$agg || $agg['sample_count'] == 0)
            return;

        // Get last_seen_price separately
        $lastPrice = (new \yii\db\Query())
            ->select('unit_price')
            ->from('pricing_dataset')
            ->where(['company_id' => $companyId, 'product_norm_name' => $productNormName])
            ->orderBy(['procurement_date' => SORT_DESC])
            ->scalar();

        // Upsert into price_analytics_summary
        $existing = PriceAnalyticsSummary::find()
            ->where(['company_id' => $companyId, 'product_norm_name' => $productNormName])
            ->one();

        if (!$existing) {
            $existing = new PriceAnalyticsSummary();
            $existing->company_id = $companyId;
            $existing->product_norm_name = $productNormName;
        }

        $existing->product_catalog_id = $agg['product_catalog_id'];
        $existing->product_category = $agg['product_category'];
        $existing->fiscal_year = $agg['fiscal_year'];
        $existing->sample_count = $agg['sample_count'];
        $existing->vendor_count = $agg['vendor_count'];
        $existing->min_price = $agg['min_price'];
        $existing->max_price = $agg['max_price'];
        $existing->avg_price = $agg['avg_price'];
        $existing->last_seen_price = $lastPrice;
        $existing->last_seen_at = $agg['last_seen_at'];
        $existing->refreshed_at = date('Y-m-d H:i:s');
        $existing->save(false);
    }

    /**
     * Rebuild vendor_price_index for a given company.
     * Should be called periodically (e.g., after all rankings are done).
     */
    public function rebuildVendorPriceIndex(int $companyId = 1): int
    {
        $vendors = (new \yii\db\Query())
            ->select(['vendor_id', 'MIN(fiscal_year) AS fiscal_year', 'MIN(product_category) AS product_category'])
            ->from('pricing_dataset')
            ->where(['company_id' => $companyId, 'is_latest_version' => 1])
            ->groupBy('vendor_id')
            ->all();

        $count = 0;
        foreach ($vendors as $v) {
            $this->updateVendorIndex($companyId, (int) $v['vendor_id']);
            $count++;
        }
        return $count;
    }

    private function updateVendorIndex(int $companyId, int $vendorId): void
    {
        $agg = (new \yii\db\Query())
            ->select([
                'COUNT(*) AS total_bids',
                'SUM(is_winner) AS total_wins',
                'ROUND(AVG(price_ratio_vs_hps), 4) AS avg_price_vs_hps',
                'ROUND(AVG(unit_price), 2) AS avg_price',
            ])
            ->from('pricing_dataset')
            ->where(['company_id' => $companyId, 'vendor_id' => $vendorId, 'is_latest_version' => 1])
            ->one();

        if (!$agg)
            return;

        $totalBids = (int) $agg['total_bids'];
        $totalWins = (int) $agg['total_wins'];
        $winRate = $totalBids > 0 ? round($totalWins / $totalBids * 100, 2) : 0;

        $existing = \app\models\VendorPriceIndex::find()
            ->where(['company_id' => $companyId, 'vendor_id' => $vendorId, 'product_catalog_id' => null])
            ->one();

        if (!$existing) {
            $existing = new \app\models\VendorPriceIndex();
            $existing->company_id = $companyId;
            $existing->vendor_id = $vendorId;
        }

        $existing->total_bids = $totalBids;
        $existing->total_wins = $totalWins;
        $existing->win_rate = $winRate;
        $existing->avg_price = $agg['avg_price'];
        $existing->avg_price_vs_hps = $agg['avg_price_vs_hps'];
        $existing->last_calculated_at = date('Y-m-d H:i:s');
        // Composite score: win_rate 40% + below_hps 40% + basic 20%
        $belowHpsFactor = $agg['avg_price_vs_hps']
            ? max(0, (1 - (float) $agg['avg_price_vs_hps']) * 100)
            : 0;
        $existing->competitiveness_score = round(
            ($winRate * 0.4) + ($belowHpsFactor * 0.4) + 20,
            2
        );
        $existing->save(false);
    }
}
