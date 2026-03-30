<?php

namespace app\models;

use Yii;

/**
 * Model for table "vendor_price_index".
 *
 * @property int $id
 * @property int $company_id
 * @property int $vendor_id
 * @property int|null $product_catalog_id
 * @property string|null $product_category
 * @property int|null $fiscal_year
 * @property int $total_bids
 * @property int $total_wins
 * @property float|null $win_rate
 * @property float|null $avg_price_rank
 * @property float|null $avg_price
 * @property float|null $avg_price_vs_hps
 * @property float|null $competitiveness_score
 * @property string|null $last_calculated_at
 */
class VendorPriceIndex extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'vendor_price_index';
    }

    public function rules()
    {
        return [
            [['company_id', 'vendor_id'], 'required'],
            [['company_id', 'vendor_id', 'product_catalog_id', 'fiscal_year', 'total_bids', 'total_wins'], 'integer'],
            [['win_rate', 'avg_price_rank', 'avg_price', 'avg_price_vs_hps', 'competitiveness_score'], 'number'],
            [['last_calculated_at'], 'safe'],
            [['product_category'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company',
            'vendor_id' => 'Vendor',
            'product_catalog_id' => 'Produk',
            'product_category' => 'Kategori',
            'fiscal_year' => 'Tahun',
            'total_bids' => 'Total Penawaran',
            'total_wins' => 'Total Menang',
            'win_rate' => 'Win Rate (%)',
            'avg_price_rank' => 'Rata-rata Rank Harga',
            'avg_price' => 'Rata-rata Harga',
            'avg_price_vs_hps' => 'Rasio Harga vs HPS',
            'competitiveness_score' => 'Skor Kompetitif',
            'last_calculated_at' => 'Terakhir Dihitung',
        ];
    }

    public function getVendor()
    {
        return $this->hasOne(MinikompetisiVendor::class, ['id' => 'vendor_id']);
    }

    /**
     * Get competitiveness leaderboard for a company.
     */
    public static function getLeaderboard(int $companyId = 1, ?int $fiscalYear = null): array
    {
        $query = self::find()
            ->with('vendor')
            ->where(['company_id' => $companyId, 'product_catalog_id' => null])
            ->orderBy(['competitiveness_score' => SORT_DESC]);

        if ($fiscalYear) {
            $query->andWhere(['fiscal_year' => $fiscalYear]);
        }

        return $query->limit(50)->all();
    }
}
