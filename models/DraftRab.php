<?php
namespace app\models;
use Yii;
class DraftRab extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'draft_rab';
    }
    public function rules()
    {
        return [
            [['tahun_anggaran', 'kode_program', 'nama_program', 'kode_kegiatan', 'nama_kegiatan', 'kode_rekening', 'uraian_anggaran', 'jumlah_anggaran', 'sumber_dana'], 'required'],
            [['tahun_anggaran', 'created_by', 'updated_by', 'is_completed'], 'integer'],
            [['jumlah_anggaran', 'sisa_anggaran'], 'number'],
            [['created_at', 'updated_at'], 'string'],
            [['kode_program', 'nama_program', 'kode_kegiatan', 'nama_kegiatan', 'kode_rekening', 'uraian_anggaran', 'sumber_dana'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tahun_anggaran' => 'Tahun Anggaran',
            'kode_program' => 'Kode Program',
            'nama_program' => 'Nama Program',
            'kode_kegiatan' => 'Kode Kegiatan',
            'nama_kegiatan' => 'Nama Kegiatan',
            'kode_rekening' => 'Kode Rekening',
            'uraian_anggaran' => 'Uraian Anggaran',
            'jumlah_anggaran' => 'Jumlah Anggaran',
            'sisa_anggaran' => 'Sisa Anggaran',
            'sumber_dana' => 'Sumber Dana',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_completed' => 'Is Completed',
        ];
    }
}