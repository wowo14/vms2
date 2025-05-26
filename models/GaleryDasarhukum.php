<?php
namespace app\models;
use Yii;
class GaleryDasarhukum extends \yii\db\ActiveRecord {
    use GeneralModelsTrait;
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'galery_dasarhukum';
    }
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['judul', 'summary', 'is_active'], 'required'],
            [['summary', 'foto', 'file_pdf', 'tags', 'created_at', 'updated_at', 'tanggal_ditetapkan'], 'string'],
            [['is_active', 'created_by', 'updated_by'], 'integer'],
            [['judul', 'kategori', 'nomor', 'penerbit'], 'string', 'max' => 255],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'judul' => 'Judul',
            'summary' => 'Summary',
            'foto' => 'Foto',
            'file_pdf' => 'File Pdf',
            'tags' => 'Tags',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'kategori' => 'Kategori',
            'nomor' => 'Nomor',
            'tanggal_ditetapkan' => 'Tanggal Ditetapkan',
            'penerbit' => 'Penerbit',
        ];
    }
    public function getAttachments() {
        return $this->hasMany(Attachment::class, ['user_id' => 'id'])
            ->andWhere(['jenis_dokumen' => 0])
            ->cache(self::cachetime(), self::settagdep('tag_attachment'));
    }
}
