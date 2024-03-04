<?php
use yii\db\Migration;
/**
 * Class m240302_010937_tblPenawaran
 */
class m240302_010937_tblPenawaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // create table penawaran pengadaan
        $this->createTable('penawaran_pengadaan', [
            'id' => $this->primaryKey(),
            'paket_id' => $this->integer()->notNull(),
            'penyedia_id' => $this->integer()->notNull(),
            'nomor' => $this->string(50),
            'kode' => $this->string(50),
            'tanggal_mendaftar' => $this->date()->notNull(),
            'ip_client' => $this->string(255),
            'masa_berlaku' => $this->string(255)->notNull(),
            'lampiran_penawaran' => $this->text(),
            'lampiran_penawaran_harga' => $this->text(),
            'penilaian'=>$this->integer(),
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240302_010937_tblPenawaran cannot be reverted.\n";
        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    }
    public function down()
    {
        echo "m240302_010937_tblPenawaran cannot be reverted.\n";
        return false;
    }
    */
}
