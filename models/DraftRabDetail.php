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
    public function getParent() {
        return $this->hasOne(DraftRab::class, ['id' => 'rab_id'])->cache(self::cachetime(), self::settagdep('tag_draftrab'));
    }
    public function getProduk() {
        return $this->hasOne(Produk::class, ['id' => 'produk_id'])->cache(self::cachetime(), self::settagdep('tag_produk'));
    }
    public function getSubtotal() {
        if ($this->harga_satuan !== 0 || $this->volume !== 0)
            return $this->volume * $this->harga_satuan;
    }
}