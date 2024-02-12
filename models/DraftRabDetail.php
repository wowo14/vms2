<?php
namespace app\models;
use Yii;
class DraftRabDetail extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'draft_rab_detail';
    }
    public function rules()
    {
        return [
            [['rab_id', 'produk_id', 'volume', 'satuan'], 'required'],
            [['rab_id', 'produk_id', 'created_by', 'updated_by'], 'integer'],
            [['volume', 'harga_satuan'], 'number'],
            [['keterangan', 'created_at', 'updated_at', 'reff_usulan'], 'string'],
            [['satuan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rab_id' => 'Rab ID',
            'produk_id' => 'Produk ID',
            'volume' => 'Volume',
            'satuan' => 'Satuan',
            'harga_satuan' => 'Harga Satuan',
            'keterangan' => 'Keterangan',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'reff_usulan' => 'Reff Usulan',
        ];
    }
}