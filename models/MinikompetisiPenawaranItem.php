<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "minikompetisi_penawaran_item".
 *
 * @property int $id
 * @property int $penawaran_id
 * @property int $item_id
 * @property float|null $harga_penawaran
 * @property float|null $skor_kualitas
 * @property string|null $keterangan
 *
 * @property MinikompetisiItem $item
 * @property MinikompetisiPenawaran $penawaran
 */
class MinikompetisiPenawaranItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'minikompetisi_penawaran_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['penawaran_id', 'item_id'], 'required'],
            [['penawaran_id', 'item_id'], 'integer'],
            [['harga_penawaran', 'skor_kualitas'], 'number'],
            [['keterangan'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penawaran_id' => 'Penawaran',
            'item_id' => 'Item Produk',
            'harga_penawaran' => 'Harga Penawaran',
            'skor_kualitas' => 'Skor Kualitas (0-100)',
            'keterangan' => 'Keterangan Kualitas',
        ];
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(MinikompetisiItem::class, ['id' => 'item_id']);
    }

    /**
     * Gets query for [[Penawaran]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPenawaran()
    {
        return $this->hasOne(MinikompetisiPenawaran::class, ['id' => 'penawaran_id']);
    }
}
