<?php

use yii\db\Migration;

/*
 * Class m231228_031445_altermenuicon
 */

class m231228_031445_altermenuicon extends Migration
{
    /*
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // add column icon to table menu
        // $this->addColumn('menu', 'icon', $this->string(255));
    }

    /*
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231228_031445_altermenuicon cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231228_031445_altermenuicon cannot be reverted.\n";

        return false;
    }
    */
}
