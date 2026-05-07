<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "minikompetisi_item".
 *
 * @property int $id
 * @property int $minikompetisi_id
 * @property string $nama_produk
 * @property float $qty
 * @property string|null $satuan
 * @property float|null $harga_hps
 * @property float|null $harga_existing
 * @property string|null $link_katalog
 *
 * @property Minikompetisi $minikompetisi
 * @property MinikompetisiPenawaranItem[] $minikompetisiPenawaranItems
 */
class MinikompetisiItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'minikompetisi_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['minikompetisi_id', 'nama_produk', 'qty'], 'required'],
            [['minikompetisi_id'], 'integer'],
            [['qty', 'harga_hps', 'harga_existing'], 'number'],
            [['nama_produk'], 'string', 'max' => 255],
            [['satuan'], 'string', 'max' => 50],
            [['link_katalog'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'minikompetisi_id' => 'Minikompetisi ID',
            'nama_produk' => 'Nama Produk',
            'qty' => 'Kuantitas',
            'satuan' => 'Satuan',
            'harga_hps' => 'Harga HPS',
            'harga_existing' => 'Harga Existing',
            'link_katalog' => 'Link Katalog',
        ];
    }

    /**
     * Gets query for [[Minikompetisi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMinikompetisi()
    {
        return $this->hasOne(Minikompetisi::class, ['id' => 'minikompetisi_id']);
    }

    /**
     * Gets query for [[MinikompetisiPenawaranItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMinikompetisiPenawaranItems()
    {
        return $this->hasMany(MinikompetisiPenawaranItem::class, ['item_id' => 'id']);
    }
}
