<?php

namespace app\models;

use Yii;

/**
 * Model for table "product_catalog" — master normalized product names.
 *
 * @property int $id
 * @property int $company_id
 * @property string $canonical_name
 * @property string|null $category
 * @property string|null $sub_category
 * @property string|null $default_unit
 * @property string|null $description
 * @property int $is_active
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property ProductAlias[] $aliases
 */
class ProductCatalog extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'product_catalog';
    }

    public function rules()
    {
        return [
            [['company_id', 'canonical_name'], 'required'],
            [['company_id', 'is_active'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['canonical_name'], 'string', 'max' => 255],
            [['category', 'sub_category', 'default_unit'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company',
            'canonical_name' => 'Nama Resmi (Canonical)',
            'category' => 'Kategori',
            'sub_category' => 'Sub Kategori',
            'default_unit' => 'Satuan Default',
            'description' => 'Deskripsi',
            'is_active' => 'Aktif',
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s');
            $this->company_id = $this->company_id ?: 1;
        } else {
            $this->updated_at = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    public function getAliases()
    {
        return $this->hasMany(ProductAlias::class, ['product_catalog_id' => 'id']);
    }

    /**
     * Find catalog by exact alias match (case-insensitive).
     */
    public static function findByAlias(string $name, int $companyId = 1): ?self
    {
        $norm = mb_strtolower(trim($name));
        $alias = ProductAlias::find()
            ->where(['alias_name' => $norm])
            ->one();

        if ($alias) {
            return self::find()
                ->where(['id' => $alias->product_catalog_id, 'company_id' => $companyId])
                ->one();
        }

        // Try direct canonical name match
        return self::find()
            ->where(['company_id' => $companyId])
            ->andWhere(['like', 'LOWER(canonical_name)', $norm])
            ->one();
    }

    /**
     * Fuzzy search using PHP similar_text.
     * Returns best match with score >= $threshold (0-100).
     */
    public static function fuzzyFind(string $rawName, int $companyId = 1, float $threshold = 65.0): ?array
    {
        $norm = ProductNormalizerService::normalize($rawName);
        $catalog = self::find()
            ->select(['id', 'canonical_name', 'category'])
            ->where(['company_id' => $companyId, 'is_active' => 1])
            ->asArray()
            ->all();

        $best = null;
        $bestScore = 0;
        foreach ($catalog as $item) {
            similar_text($norm, ProductNormalizerService::normalize($item['canonical_name']), $pct);
            if ($pct > $bestScore && $pct >= $threshold) {
                $best = $item;
                $bestScore = $pct;
            }
        }
        return $best ? array_merge($best, ['score' => $bestScore]) : null;
    }
}
