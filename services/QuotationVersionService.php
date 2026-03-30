<?php

namespace app\services;

use app\models\QuotationVersion;
use app\models\QuotationItem;
use app\models\MinikompetisiItem;
use Yii;

/**
 * QuotationVersionService — manages versioned vendor quotations.
 * 
 * Core rule: NEVER delete existing quotations. Always append a new version.
 */
class QuotationVersionService
{
    /**
     * Create a new quotation version for a vendor.
     * Marks all previous versions (same minikompetisi + vendor) as is_latest=0.
     *
     * @param int    $minikompetisiId
     * @param int    $vendorId
     * @param string|null $filePath     Saved file path on disk
     * @param string $revisionNote     User-entered revision reason
     * @param int|null $uploadedBy     User ID
     * @return QuotationVersion
     * @throws \Exception
     */
    public function createVersion(
        int $minikompetisiId,
        int $vendorId,
        ?string $filePath,
        string $revisionNote = '',
        ?int $uploadedBy = null
    ): QuotationVersion {
        $db = Yii::$app->db;

        // Step 1: Mark all previous versions as not latest
        $db->createCommand(
            'UPDATE {{%quotation_version}} SET is_latest = 0
             WHERE minikompetisi_id = :mid AND vendor_id = :vid'
        )->bindValues([':mid' => $minikompetisiId, ':vid' => $vendorId])->execute();

        // Step 2: Determine next version number
        $nextVer = (int) $db->createCommand(
            'SELECT COALESCE(MAX(version_number), 0) + 1
             FROM {{%quotation_version}}
             WHERE minikompetisi_id = :mid AND vendor_id = :vid'
        )->bindValues([':mid' => $minikompetisiId, ':vid' => $vendorId])->queryScalar();

        // Step 3: Create new version
        $version = new QuotationVersion();
        $version->minikompetisi_id = $minikompetisiId;
        $version->vendor_id = $vendorId;
        $version->version_number = $nextVer;
        $version->version_label = 'v' . $nextVer;
        $version->revision_note = $revisionNote ?: null;
        $version->quotation_file_path = $filePath;
        $version->is_latest = 1;
        $version->status = QuotationVersion::STATUS_SUBMITTED;
        $version->uploaded_at = date('Y-m-d H:i:s');
        $version->uploaded_by = $uploadedBy ?? (Yii::$app->user->isGuest ? null : Yii::$app->user->id);

        if (!$version->save()) {
            throw new \Exception('Gagal menyimpan versi penawaran: ' . json_encode($version->errors));
        }

        return $version;
    }

    /**
     * Attach structured quotation items to a version.
     * Each row: ['item_id' => int, 'harga_penawaran' => float, 'skor_kualitas' => float, 'keterangan' => string]
     *
     * @param QuotationVersion $version
     * @param array            $rows
     * @return float Total harga (sum of quantity * unit_price)
     */
    public function attachItems(QuotationVersion $version, array $rows): float
    {
        $totalHarga = 0;

        foreach ($rows as $row) {
            $mItem = MinikompetisiItem::findOne($row['item_id'] ?? 0);
            if (!$mItem)
                continue;

            $unitPrice = (float) ($row['harga_penawaran'] ?? 0);

            // Suggest catalog via normalizer
            $suggestion = ProductNormalizerService::suggestCatalog(
                $mItem->nama_produk,
                1  // company_id — extend to dynamic if needed
            );

            $qi = new QuotationItem();
            $qi->quotation_version_id = $version->id;
            $qi->minikompetisi_item_id = $mItem->id;
            $qi->product_catalog_id = $suggestion['catalog_id'];
            $qi->product_name = $mItem->nama_produk;
            $qi->product_name_norm = ProductNormalizerService::normalize($mItem->nama_produk);
            $qi->product_category = $mItem->product_category;
            $qi->specification = $mItem->specification;
            $qi->unit = $mItem->satuan;
            $qi->quantity = (float) $mItem->qty;
            $qi->unit_price = $unitPrice;
            $qi->skor_kualitas = (float) ($row['skor_kualitas'] ?? 0);
            $qi->keterangan = $row['keterangan'] ?? '';
            // total_price auto-computed in beforeSave()

            $qi->save(false); // skip validation for bulk speed

            $totalHarga += ($unitPrice * (float) $mItem->qty);
        }

        return $totalHarga;
    }

    /**
     * Get revision history for a vendor in a procurement event.
     */
    public function getHistory(int $minikompetisiId, int $vendorId): array
    {
        return QuotationVersion::getHistory($minikompetisiId, $vendorId);
    }

    /**
     * Get latest versions for all vendors in a procurement event
     * (for ranking calculation).
     */
    public function getLatestVersions(int $minikompetisiId): array
    {
        return QuotationVersion::find()
            ->with(['vendor', 'quotationItems'])
            ->where(['minikompetisi_id' => $minikompetisiId, 'is_latest' => 1])
            ->all();
    }
}
