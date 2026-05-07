<?php

namespace app\models;

use Yii;

/**
 * Model for table "quotation_item" — structured per-item pricing per quotation version.
 *
 * @property int $id
 * @property int $quotation_version_id
 * @property int $minikompetisi_item_id
 * @property int|null $product_catalog_id
 * @property string $product_name
 * @property string|null $product_name_norm
 * @property string|null $product_category
 * @property string|null $specification
 * @property string|null $unit
 * @property float $quantity
 * @property float $unit_price
 * @property float|null $total_price
 * @property float|null $skor_kualitas
 * @property string|null $keterangan
 * @property string|null $link_katalog
 *
 * @property QuotationVersion $quotationVersion
 * @property MinikompetisiItem $minikompetisiItem
 * @property ProductCatalog $catalog
 */
class QuotationItem extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'quotation_item';
    }

    public function rules()
    {
        return [
            [['quotation_version_id', 'minikompetisi_item_id', 'product_name', 'quantity', 'unit_price'], 'required'],
            [['quotation_version_id', 'minikompetisi_item_id', 'product_catalog_id'], 'integer'],
            [['quantity', 'unit_price', 'total_price', 'skor_kualitas'], 'number'],
            [['product_name', 'product_name_norm'], 'string', 'max' => 255],
            [['product_category', 'unit'], 'string', 'max' => 100],
            [['specification', 'keterangan', 'link_katalog'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quotation_version_id' => 'Versi Penawaran',
            'minikompetisi_item_id' => 'Item Produk',
            'product_catalog_id' => 'Katalog Produk',
            'product_name' => 'Nama Produk',
            'product_name_norm' => 'Nama Ternormalisasi',
            'product_category' => 'Kategori',
            'unit' => 'Satuan',
            'quantity' => 'Kuantitas',
            'unit_price' => 'Harga Satuan',
            'total_price' => 'Total Harga',
            'skor_kualitas' => 'Skor Kualitas',
            'keterangan' => 'Keterangan',
            'link_katalog' => 'Link Katalog',
        ];
    }

    public function beforeSave($insert)
    {
        // Auto-compute total_price since SQLite has no GENERATED ALWAYS
        $this->total_price = round((float) $this->quantity * (float) $this->unit_price, 2);
        return parent::beforeSave($insert);
    }

    public function getQuotationVersion()
    {
        return $this->hasOne(QuotationVersion::class, ['id' => 'quotation_version_id']);
    }

    public function getMinikompetisiItem()
    {
        return $this->hasOne(MinikompetisiItem::class, ['id' => 'minikompetisi_item_id']);
    }

    public function getCatalog()
    {
        return $this->hasOne(ProductCatalog::class, ['id' => 'product_catalog_id']);
    }
}
