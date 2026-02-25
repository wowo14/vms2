<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "minikompetisi_vendor".
 *
 * @property int $id
 * @property int $minikompetisi_id
 * @property string $nama_vendor
 * @property string|null $email_vendor
 *
 * @property Minikompetisi $minikompetisi
 * @property MinikompetisiPenawaran[] $minikompetisiPenawarans
 */
class MinikompetisiVendor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'minikompetisi_vendor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['minikompetisi_id', 'nama_vendor'], 'required'],
            [['minikompetisi_id'], 'integer'],
            [['nama_vendor', 'email_vendor'], 'string', 'max' => 255],
            [['email_vendor'], 'email'],
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
            'nama_vendor' => 'Nama Vendor',
            'email_vendor' => 'Email Vendor',
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
     * Gets query for [[MinikompetisiPenawarans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMinikompetisiPenawarans()
    {
        return $this->hasMany(MinikompetisiPenawaran::class, ['vendor_id' => 'id']);
    }
}
