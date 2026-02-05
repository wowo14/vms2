<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%report_penyedia}}`.
 */
class m260205_044726_create_report_penyedia_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%report_penyedia}}', [
            'id' => $this->primaryKey(),
            'penyedia_id' => $this->integer(),
            'penilaian_id' => $this->integer(),
            'nama_penyedia' => $this->string(),
            'alamat' => $this->text(),
            'kota' => $this->string(),
            'telepon' => $this->string(),
            'produk_ditawarkan' => $this->text(),
            'jenis_pekerjaan' => $this->string(),
            'nama_paket' => $this->string(),
            'bidang' => $this->string(),
            'nilai_evaluasi' => $this->decimal(10, 2),
            'source' => $this->string()->defaultValue('system'),
            'created_at' => $this->datetime(),
            'updated_at' => $this->datetime(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->createIndex('idx-report_penyedia-penyedia_id', '{{%report_penyedia}}', 'penyedia_id');
        $this->createIndex('idx-report_penyedia-penilaian_id', '{{%report_penyedia}}', 'penilaian_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%report_penyedia}}');
    }
}
