<?php
use yii\db\Migration;
class m250116_063327_penilaianPenyedia extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('penilaian_penyedia', [
            'id' => $this->primaryKey(), // auto-increment primary key
            'unit_kerja' => $this->string(),
            'nama_perusahaan' => $this->string(),
            'alamat_perusahaan' => $this->string(), // required field
            'paket_pekerjaan' => $this->string(), // required field
            'lokasi_pekerjaan' => $this->string(),
            'nomor_kontrak' => $this->string()->notNull(), // required field
            'jangka_waktu' => $this->string(),
            'tanggal_kontrak' => $this->dateTime(),
            'metode_pemilihan' => $this->string(),
            'details' => $this->text(),
            'pengguna_anggaran' => $this->string(),
            'pejabat_pembuat_komitmen' => $this->string(),
            'nilai_kontrak' => $this->decimal(15, 2), // For 'number' field type
            'dpp_id' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250116_063327_penilaianPenyedia cannot be reverted.\n";
        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    }
    public function down()
    {
        echo "m250116_063327_penilaianPenyedia cannot be reverted.\n";
        return false;
    }
    */
}
