<?php

namespace app\models;

use Yii;

/**
 * Model for table "quotation_version".
 * Replaces minikompetisi_penawaran with append-only versioning.
 *
 * @property int $id
 * @property int $minikompetisi_id
 * @property int $vendor_id
 * @property int $version_number
 * @property string|null $version_label
 * @property string|null $revision_note
 * @property string|null $quotation_file_path
 * @property int $status
 * @property int $is_latest
 * @property string|null $uploaded_at
 * @property int|null $uploaded_by
 * @property float|null $total_harga
 * @property float|null $total_skor_kualitas
 * @property float|null $total_skor_harga
 * @property float|null $total_skor_akhir
 * @property int|null $ranking
 * @property int $is_winner
 *
 * @property Minikompetisi $minikompetisi
 * @property MinikompetisiVendor $vendor
 * @property QuotationItem[] $quotationItems
 */
class QuotationVersion extends \yii\db\ActiveRecord
{
    const STATUS_SUBMITTED = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REJECTED = 3;

    public static function tableName()
    {
        return 'quotation_version';
    }

    public function rules()
    {
        return [
            [['minikompetisi_id', 'vendor_id'], 'required'],
            [
                [
                    'minikompetisi_id',
                    'vendor_id',
                    'version_number',
                    'status',
                    'ranking',
                    'uploaded_by',
                    'is_latest',
                    'is_winner'
                ],
                'integer'
            ],
            [['revision_note', 'quotation_file_path'], 'string'],
            [['total_harga', 'total_skor_kualitas', 'total_skor_harga', 'total_skor_akhir'], 'number'],
            [['uploaded_at'], 'safe'],
            [['version_label'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'minikompetisi_id' => 'Paket Minikompetisi',
            'vendor_id' => 'Vendor',
            'version_number' => 'Versi',
            'version_label' => 'Label Versi',
            'revision_note' => 'Catatan Revisi',
            'quotation_file_path' => 'File Penawaran',
            'status' => 'Status',
            'is_latest' => 'Versi Terbaru?',
            'uploaded_at' => 'Waktu Upload',
            'uploaded_by' => 'Di-upload Oleh',
            'total_harga' => 'Total Harga',
            'total_skor_kualitas' => 'Skor Kualitas',
            'total_skor_harga' => 'Skor Harga',
            'total_skor_akhir' => 'Skor Akhir',
            'ranking' => 'Ranking',
            'is_winner' => 'Pemenang?',
        ];
    }

    public function getMinikompetisi()
    {
        return $this->hasOne(Minikompetisi::class, ['id' => 'minikompetisi_id']);
    }

    public function getVendor()
    {
        return $this->hasOne(MinikompetisiVendor::class, ['id' => 'vendor_id']);
    }

    public function getQuotationItems()
    {
        return $this->hasMany(QuotationItem::class, ['quotation_version_id' => 'id']);
    }

    public function getStatusText(): string
    {
        return [
            0 => 'Draft',
            1 => 'Disubmit',
            2 => 'Diterima',
            3 => 'Ditolak',
        ][$this->status] ?? 'Unknown';
    }

    /**
     * Get full revision history for a vendor in a procurement event.
     * Ordered oldest to newest.
     */
    public static function getHistory(int $minikompetisiId, int $vendorId): array
    {
        return self::find()
            ->where([
                'minikompetisi_id' => $minikompetisiId,
                'vendor_id' => $vendorId,
            ])
            ->orderBy(['version_number' => SORT_ASC])
            ->all();
    }

    /**
     * Get the latest version for each vendor in a procurement event.
     * Returns indexed by vendor_id.
     */
    public static function getLatestByProcurement(int $minikompetisiId): array
    {
        return self::find()
            ->where([
                'minikompetisi_id' => $minikompetisiId,
                'is_latest' => 1,
            ])
            ->indexBy('vendor_id')
            ->all();
    }
}
