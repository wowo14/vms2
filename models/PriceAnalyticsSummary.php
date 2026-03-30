<?php

namespace app\models;

use Yii;

/**
 * Model for table "price_analytics_summary".
 *
 * @property int $id
 * @property int $company_id
 * @property int|null $product_catalog_id
 * @property string $product_norm_name
 * @property string|null $product_category
 * @property int|null $fiscal_year
 * @property int $sample_count
 * @property int $vendor_count
 * @property float|null $min_price
 * @property float|null $max_price
 * @property float|null $avg_price
 * @property float|null $last_seen_price
 * @property string|null $last_seen_at
 * @property string|null $refreshed_at
 */
class PriceAnalyticsSummary extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'price_analytics_summary';
    }

    public function rules()
    {
        return [
            [['company_id', 'product_norm_name'], 'required'],
            [['company_id', 'product_catalog_id', 'fiscal_year', 'sample_count', 'vendor_count'], 'integer'],
            [['min_price', 'max_price', 'avg_price', 'last_seen_price'], 'number'],
            [['last_seen_at', 'refreshed_at'], 'safe'],
            [['product_norm_name'], 'string', 'max' => 255],
            [['product_category'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company',
            'product_norm_name' => 'Produk',
            'product_category' => 'Kategori',
            'fiscal_year' => 'Tahun',
            'sample_count' => 'Jumlah Data',
            'vendor_count' => 'Jumlah Vendor',
            'min_price' => 'Harga Min',
            'max_price' => 'Harga Maks',
            'avg_price' => 'Harga Rata-rata',
            'last_seen_price' => 'Harga Terakhir',
            'last_seen_at' => 'Terakhir Dilihat',
            'refreshed_at' => 'Diperbarui',
        ];
    }
}
