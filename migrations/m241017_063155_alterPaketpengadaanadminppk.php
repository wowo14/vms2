<?php
use yii\db\Migration;
/**
 * Class m241017_063155_alterPaketpengadaanadminppk
 */
class m241017_063155_alterPaketpengadaanadminppk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //addnew column to paketPengadaan
        $this->addColumn('paket_pengadaan', 'admin_ppkom', $this->integer());
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241017_063155_alterPaketpengadaanadminppk cannot be reverted.\n";
        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    }
    public function down()
    {
        echo "m241017_063155_alterPaketpengadaanadminppk cannot be reverted.\n";
        return false;
    }
    */
}
