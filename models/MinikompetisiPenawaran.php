<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "minikompetisi_penawaran".
 *
 * @property int $id
 * @property int $minikompetisi_id
 * @property int $vendor_id
 * @property float|null $total_harga
 * @property float|null $total_skor_kualitas
 * @property float|null $total_skor_harga
 * @property float|null $total_skor_akhir
 * @property int|null $ranking
 * @property int|null $is_winner
 * @property string|null $created_at
 *
 * @property Minikompetisi $minikompetisi
 * @property MinikompetisiVendor $vendor
 * @property MinikompetisiPenawaranItem[] $minikompetisiPenawaranItems
 */
class MinikompetisiPenawaran extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'minikompetisi_penawaran';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['minikompetisi_id', 'vendor_id'], 'required'],
            [['minikompetisi_id', 'vendor_id', 'ranking', 'is_winner'], 'integer'],
            [['total_harga', 'total_skor_kualitas', 'total_skor_harga', 'total_skor_akhir'], 'number'],
            [['created_at'], 'safe'],
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
            'vendor_id' => 'Vendor',
            'total_harga' => 'Total Harga',
            'total_skor_kualitas' => 'Total Skor Kualitas',
            'total_skor_harga' => 'Total Skor Harga',
            'total_skor_akhir' => 'Total Skor Akhir',
            'ranking' => 'Ranking',
            'is_winner' => 'Pemenang?',
            'created_at' => 'Waktu Upload',
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
     * Gets query for [[Vendor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVendor()
    {
        return $this->hasOne(MinikompetisiVendor::class, ['id' => 'vendor_id']);
    }

    /**
     * Gets query for [[MinikompetisiPenawaranItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMinikompetisiPenawaranItems()
    {
        return $this->hasMany(MinikompetisiPenawaranItem::class, ['penawaran_id' => 'id']);
    }
}
