<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "minikompetisi".
 *
 * @property int $id
 * @property string $judul
 * @property string|null $tanggal
 * @property int $metode
 * @property float|null $bobot_kualitas
 * @property float|null $bobot_harga
 * @property int|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 *
 * @property MinikompetisiItem[] $minikompetisiItems
 * @property MinikompetisiPenawaran[] $minikompetisiPenawarans
 * @property MinikompetisiVendor[] $minikompetisiVendors
 */
class Minikompetisi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'minikompetisi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['judul'], 'required'],
            [['tanggal', 'created_at', 'updated_at'], 'safe'],
            [['metode', 'status', 'created_by'], 'integer'],
            [['bobot_kualitas', 'bobot_harga'], 'number'],
            [['judul'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'judul' => 'Judul Minikompetisi',
            'tanggal' => 'Tanggal',
            'metode' => 'Metode Evaluasi',
            'bobot_kualitas' => 'Bobot Kualitas (%)',
            'bobot_harga' => 'Bobot Harga (%)',
            'status' => 'Status',
            'created_at' => 'Dibuat Pada',
            'updated_at' => 'Diperbarui Pada',
            'created_by' => 'Dibuat Oleh',
        ];
    }

    /**
     * Gets query for [[MinikompetisiItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMinikompetisiItems()
    {
        return $this->hasMany(MinikompetisiItem::class, ['minikompetisi_id' => 'id']);
    }

    /**
     * Gets query for [[MinikompetisiPenawarans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMinikompetisiPenawarans()
    {
        return $this->hasMany(MinikompetisiPenawaran::class, ['minikompetisi_id' => 'id']);
    }

    /**
     * Gets query for [[MinikompetisiVendors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMinikompetisiVendors()
    {
        return $this->hasMany(MinikompetisiVendor::class, ['minikompetisi_id' => 'id']);
    }

    public function getMetodeText()
    {
        $options = [
            1 => 'Harga Terendah',
            2 => 'Kualitas & Harga',
            3 => 'Lumpsum',
        ];
        return isset($options[$this->metode]) ? $options[$this->metode] : 'Unknown';
    }

    public function getStatusText()
    {
        $options = [
            0 => 'Draft',
            1 => 'Dipublikasikan / Berjalan',
            2 => 'Selesai',
        ];
        return isset($options[$this->status]) ? $options[$this->status] : 'Unknown';
    }
}
