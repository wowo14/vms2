<?php

namespace app\models;

use Yii;

/**
 * Model for table "pricing_dataset" — analytics data warehouse.
 *
 * @property int $id
 * @property int $company_id
 * @property int|null $fiscal_year
 * @property string $procurement_date
 * @property int $procurement_id
 * @property int $quotation_version_id
 * @property int $vendor_id
 * @property int|null $product_catalog_id
 * @property string $product_raw_name
 * @property string|null $product_norm_name
 * @property string|null $product_category
 * @property string|null $specification
 * @property string|null $location
 * @property string|null $unit
 * @property float $quantity
 * @property float $unit_price
 * @property float $total_price
 * @property float|null $hps_price
 * @property float|null $price_ratio_vs_hps
 * @property int $is_winner
 * @property int $version_number
 * @property int $is_latest_version
 * @property string|null $ingested_at
 */
class PricingDataset extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'pricing_dataset';
    }

    public function rules()
    {
        return [
            [
                [
                    'company_id',
                    'procurement_date',
                    'procurement_id',
                    'quotation_version_id',
                    'vendor_id',
                    'product_raw_name',
                    'quantity',
                    'unit_price',
                    'total_price'
                ],
                'required'
            ],
            [
                [
                    'company_id',
                    'fiscal_year',
                    'procurement_id',
                    'quotation_version_id',
                    'vendor_id',
                    'product_catalog_id',
                    'is_winner',
                    'version_number',
                    'is_latest_version'
                ],
                'integer'
            ],
            [['quantity', 'unit_price', 'total_price', 'hps_price', 'price_ratio_vs_hps'], 'number'],
            [['procurement_date', 'ingested_at'], 'safe'],
            [['product_raw_name', 'product_norm_name'], 'string', 'max' => 255],
            [['product_category', 'location', 'unit'], 'string', 'max' => 100],
            [['specification'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company',
            'fiscal_year' => 'Tahun Anggaran',
            'procurement_date' => 'Tanggal Pengadaan',
            'procurement_id' => 'ID Paket',
            'quotation_version_id' => 'Versi Penawaran',
            'vendor_id' => 'Vendor',
            'product_catalog_id' => 'Katalog Produk',
            'product_raw_name' => 'Nama Produk (Asli)',
            'product_norm_name' => 'Nama Produk (Normal)',
            'product_category' => 'Kategori',
            'unit' => 'Satuan',
            'quantity' => 'Qty',
            'unit_price' => 'Harga Satuan',
            'total_price' => 'Total Harga',
            'hps_price' => 'HPS Satuan',
            'price_ratio_vs_hps' => 'Rasio vs HPS',
            'is_winner' => 'Pemenang',
            'is_latest_version' => 'Versi Terbaru',
        ];
    }

    public function getVendor()
    {
        return $this->hasOne(MinikompetisiVendor::class, ['id' => 'vendor_id']);
    }

    public function getCatalog()
    {
        return $this->hasOne(ProductCatalog::class, ['id' => 'product_catalog_id']);
    }

    // ──────────────────────────────────────────────
    // Analytics Query Helpers
    // ──────────────────────────────────────────────

    /**
     * Find cheapest vendors for a product name (partial match).
     */
    public static function findCheapestVendors(string $productTerm, int $companyId = 1): array
    {
        return (new \yii\db\Query())
            ->select([
                'v.nama_vendor',
                'pd.vendor_id',
                'MIN(pd.unit_price) AS min_price',
                'ROUND(AVG(pd.unit_price), 2) AS avg_price',
                'COUNT(*) AS bid_count',
            ])
            ->from(['pd' => 'pricing_dataset'])
            ->leftJoin(['v' => 'minikompetisi_vendor'], 'v.id = pd.vendor_id')
            ->where(['pd.company_id' => $companyId, 'pd.is_latest_version' => 1])
            ->andWhere(['like', 'pd.product_norm_name', $productTerm])
            ->groupBy(['pd.vendor_id', 'v.nama_vendor'])
            ->orderBy(['min_price' => SORT_ASC])
            ->all();
    }

    /**
     * Get historical price range for a product.
     */
    public static function getPriceRange(string $productTerm, int $companyId = 1): ?array
    {
        return (new \yii\db\Query())
            ->select([
                'product_norm_name',
                'MIN(unit_price) AS min_price',
                'ROUND(AVG(unit_price), 2) AS avg_price',
                'MAX(unit_price) AS max_price',
                'COUNT(*) AS total_samples',
                'COUNT(DISTINCT vendor_id) AS vendor_count',
                'MIN(procurement_date) AS first_seen',
                'MAX(procurement_date) AS last_seen',
            ])
            ->from('pricing_dataset')
            ->where(['company_id' => $companyId])
            ->andWhere(['like', 'product_norm_name', $productTerm])
            ->one();
    }

    /**
     * Get price trend over time (monthly).
     */
    public static function getPriceTrend(string $productTerm, int $companyId = 1): array
    {
        return (new \yii\db\Query())
            ->select([
                "SUBSTR(procurement_date, 1, 7) AS year_month",
                'ROUND(AVG(unit_price), 2) AS avg_price',
                'MIN(unit_price) AS min_price',
                'COUNT(*) AS sample_count',
            ])
            ->from('pricing_dataset')
            ->where(['company_id' => $companyId])
            ->andWhere(['like', 'product_norm_name', $productTerm])
            ->groupBy("SUBSTR(procurement_date, 1, 7)")
            ->orderBy("year_month ASC")
            ->all();
    }

    /**
     * Get prices below HPS (good deals) for a product.
     */
    public static function getBelowHps(string $productTerm, int $companyId = 1): array
    {
        return (new \yii\db\Query())
            ->select([
                'v.nama_vendor',
                'pd.product_norm_name',
                'pd.unit_price',
                'pd.hps_price',
                'pd.price_ratio_vs_hps',
                'pd.procurement_date',
            ])
            ->from(['pd' => 'pricing_dataset'])
            ->leftJoin(['v' => 'minikompetisi_vendor'], 'v.id = pd.vendor_id')
            ->where(['pd.company_id' => $companyId])
            ->andWhere(['like', 'pd.product_norm_name', $productTerm])
            ->andWhere(['IS NOT', 'pd.price_ratio_vs_hps', null])
            ->andWhere(['<', 'pd.price_ratio_vs_hps', 1.0])
            ->orderBy(['pd.price_ratio_vs_hps' => SORT_ASC])
            ->limit(20)
            ->all();
    }
}
