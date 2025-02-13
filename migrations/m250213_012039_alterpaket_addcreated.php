<?php

use yii\db\Migration;

class m250213_012039_alterpaket_addcreated extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('paket_pengadaan', 'created_at', $this->dateTime());
        $this->addColumn('paket_pengadaan', 'updated_at', $this->dateTime());
        $this->addColumn('dpp', 'tanggal_terima', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250213_012039_alterpaket_addcreated cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250213_012039_alterpaket_addcreated cannot be reverted.\n";

        return false;
    }
    */
}
