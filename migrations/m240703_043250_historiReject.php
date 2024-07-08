<?php

use yii\db\Migration;

/**
 * Class m240703_043250_historiReject
 */
class m240703_043250_historiReject extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('histori_reject', [
            'id' => $this->primaryKey(),
            'dpp_id' => $this->integer(),
            'user_id' => $this->integer(),
            'created_at' => $this->integer(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240703_043250_historiReject cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240703_043250_historiReject cannot be reverted.\n";

        return false;
    }
    */
}
