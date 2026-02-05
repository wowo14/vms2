<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "report_penyedia".
 *
 * @property int $id
 * @property int|null $penyedia_id
 * @property int|null $penilaian_id
 * @property string|null $nama_penyedia
 * @property string|null $alamat
 * @property string|null $kota
 * @property string|null $telepon
 * @property string|null $produk_ditawarkan
 * @property string|null $jenis_pekerjaan
 * @property string|null $nama_paket
 * @property string|null $bidang
 * @property float|null $nilai_evaluasi
 * @property string|null $source
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class ReportPenyedia extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_penyedia';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['penyedia_id', 'penilaian_id', 'created_by', 'updated_by'], 'integer'],
            [['alamat', 'produk_ditawarkan'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['nama_penyedia', 'kota', 'telepon', 'jenis_pekerjaan', 'nama_paket', 'bidang', 'source', 'nilai_evaluasi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penyedia_id' => 'Penyedia ID',
            'penilaian_id' => 'Penilaian ID',
            'nama_penyedia' => 'Nama Penyedia',
            'alamat' => 'Alamat',
            'kota' => 'Kota',
            'telepon' => 'Telepon',
            'produk_ditawarkan' => 'Produk ditawarkan',
            'jenis_pekerjaan' => 'Jenis pekerjaan',
            'nama_paket' => 'Nama Paket',
            'bidang' => 'Bidang',
            'nilai_evaluasi' => 'Nilai Evaluasi',
            'source' => 'Source',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = date('Y-m-d H:i:s');
                $this->created_by = (isset(Yii::$app->user)) ? Yii::$app->user->id : null;
            } else {
                $this->updated_at = date('Y-m-d H:i:s');
                $this->updated_by = (isset(Yii::$app->user)) ? Yii::$app->user->id : null;
            }
            return true;
        }
        return false;
    }

    /**
     * Get related Penyedia
     */
    public function getPenyedia()
    {
        return $this->hasOne(Penyedia::class, ['id' => 'penyedia_id']);
    }

    /**
     * Get related Penilaian
     */
    public function getPenilaian()
    {
        return $this->hasOne(PenilaianPenyedia::class, ['id' => 'penilaian_id']);
    }
}
