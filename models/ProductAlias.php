<?php

namespace app\models;

use Yii;

/**
 * Model for table "product_alias" — variant names for fuzzy matching.
 *
 * @property int $id
 * @property int $product_catalog_id
 * @property string $alias_name
 *
 * @property ProductCatalog $catalog
 */
class ProductAlias extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'product_alias';
    }

    public function rules()
    {
        return [
            [['product_catalog_id', 'alias_name'], 'required'],
            [['product_catalog_id'], 'integer'],
            [['alias_name'], 'string', 'max' => 255],
            // Normalize before save
            ['alias_name', 'filter', 'filter' => fn($v) => mb_strtolower(trim($v))],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_catalog_id' => 'Produk',
            'alias_name' => 'Alias / Nama Lain',
        ];
    }

    public function getCatalog()
    {
        return $this->hasOne(ProductCatalog::class, ['id' => 'product_catalog_id']);
    }

    /**
     * Upsert alias: save or update product_catalog_id if alias already exists.
     */
    public static function upsert(int $catalogId, string $alias): void
    {
        $norm = mb_strtolower(trim($alias));
        $existing = self::find()->where(['alias_name' => $norm])->one();
        if ($existing) {
            $existing->product_catalog_id = $catalogId;
            $existing->save(false);
        } else {
            $obj = new self();
            $obj->product_catalog_id = $catalogId;
            $obj->alias_name = $norm;
            $obj->save(false);
        }
    }
}
