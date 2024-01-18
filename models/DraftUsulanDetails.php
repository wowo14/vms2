<?php
namespace app\models;
use Yii;
class DraftUsulanDetails extends \yii\db\ActiveRecord
{
    use GeneralModelsTrait;
    public static function tableName()
    {
        return 'draft_usulan_details';
    }
    public function rules()
    {
        return [
            [['header_id', 'produk_id', 'qty_usulan'], 'required'],
            [['header_id', 'rab_id', 'produk_id', 'qty_usulan', 'qty_ditolak', 'qty_diterima', 'created_by', 'updated_by', 'is_completed', 'is_canceled', 'is_submitted'], 'integer'],
            [['harga_pasar', 'harga_pembelian_terakhir', 'overhead', 'nilai_overhead', 'ppn', 'harga_perencanaan', 'harga_total'], 'number'],
            [['created_at', 'updated_at'], 'string'],
            [['keterangan', 'satuan'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'header_id' => 'Header ID',
            'rab_id' => 'Rab ID',
            'produk_id' => 'Produk ID',
            'qty_usulan' => 'Qty Usulan',
            'qty_ditolak' => 'Qty Ditolak',
            'qty_diterima' => 'Qty Diterima',
            'keterangan' => 'Keterangan',
            'satuan' => 'Satuan',
            'harga_pasar' => 'Harga Pasar',
            'harga_pembelian_terakhir' => 'Harga Pembelian Terakhir',
            'overhead' => 'Overhead',
            'nilai_overhead' => 'Nilai Overhead',
            'ppn' => 'Ppn',
            'harga_perencanaan' => 'Harga Perencanaan',
            'harga_total' => 'Harga Total',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_completed' => 'Is Completed',
            'is_canceled' => 'Is Canceled',
            'is_submitted' => 'Is Submitted',
        ];
    }
    public function getProduk()
    {
        return $this->hasOne(Produk::class, ['id' => 'produk_id'])->cache(self::cachetime(), self::settagdep('tag_produk'));
    }
    public function getParent()
    {
        return $this->hasOne(DraftUsulan::class, ['id' => 'header_id'])->cache(self::cachetime(), self::settagdep('tag_draftusulan'));
    }
    public function getStatus()
    {
        if ($this->hasAttribute('is_submitted') && $this->is_submitted == 1) {
            return 'Submitted';
        } elseif ($this->hasAttribute('is_canceled') && $this->is_canceled == 1) {
            return 'Cancel';
        } elseif ($this->hasAttribute('is_completed') && $this->is_completed == 1) {
            return 'Completed';
        } else {
            return 'Draft';
        }
    }
}