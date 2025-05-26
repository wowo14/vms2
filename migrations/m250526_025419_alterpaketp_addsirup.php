<?php

use yii\db\Migration;

class m250526_025419_alterpaketp_addsirup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('paket_pengadaan', 'linksirup', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250526_025419_alterpaketp_addsirup cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250526_025419_alterpaketp_addsirup cannot be reverted.\n";

        return false;
    }
    */
}
