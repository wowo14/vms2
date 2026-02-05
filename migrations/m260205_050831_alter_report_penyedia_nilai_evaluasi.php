<?php

use yii\db\Migration;

class m260205_050831_alter_report_penyedia_nilai_evaluasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $db = $this->db;
        $table = 'report_penyedia';
        $tempTable = 'report_penyedia_temp';

        // 1. Create temp table with new schema
        $this->createTable($tempTable, [
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
            'nilai_evaluasi' => $this->string(), // Changed to string
            'source' => $this->string(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // 2. Copy data
        $db->createCommand("INSERT INTO $tempTable SELECT * FROM $table")->execute();

        // 3. Drop old table
        $this->dropTable($table);

        // 4. Rename temp to original
        $this->renameTable($tempTable, $table);

        // 5. Re-create indexes
        $this->createIndex('idx-report_penyedia-penyedia_id', 'report_penyedia', 'penyedia_id');
        $this->createIndex('idx-report_penyedia-penilaian_id', 'report_penyedia', 'penilaian_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260205_050831_alter_report_penyedia_nilai_evaluasi cannot be reverted.\n";

        return false;
    }
    */
}
