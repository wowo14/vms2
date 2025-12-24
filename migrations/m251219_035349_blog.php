<?php

use yii\db\Migration;

class m251219_035349_blog extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // create table blog
        $this->createTable('blog', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'content' => $this->text()->notNull(),
            'status' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251219_035349_blog cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251219_035349_blog cannot be reverted.\n";

        return false;
    }
    */
}
