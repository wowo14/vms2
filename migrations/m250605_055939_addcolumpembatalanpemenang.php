<?php
use yii\db\Migration;
class m250605_055939_addcolumpembatalanpemenang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('paket_pengadaan', 'dibatalkan', $this->integer());
        $this->addColumn('paket_pengadaan', 'alasan_dibatalkan', $this->text());
        $this->addColumn('paket_pengadaan', 'berita_acara_pembatalan', $this->text());
        $this->addColumn('paket_pengadaan', 'tanggal_dibatalkan', $this->string());
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250605_055939_addcolumpembatalanpemenang cannot be reverted.\n";
        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    }
    public function down()
    {
        echo "m250605_055939_addcolumpembatalanpemenang cannot be reverted.\n";
        return false;
    }
    */
}
