<?php
use yii\db\Migration;
/**
 * Class m240904_044519_alter_negosiasi_adddetail
 */
class m240904_044519_alter_negosiasi_adddetail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // alter table add column detail long text
         $this->addColumn('negosiasi','detail',$this->text());
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240904_044519_alter_negosiasi_adddetail cannot be reverted.\n";
        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    }
    public function down()
    {
        echo "m240904_044519_alter_negosiasi_adddetail cannot be reverted.\n";
        return false;
    }
    */
}
