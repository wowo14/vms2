<?php

use yii\db\Migration;

class m260513_033719_add_file_reject_to_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tables = ['review_dpp', 'paket_pengadaan', 'histori_reject'];
        foreach ($tables as $table) {
            if ($this->db->schema->getTableSchema($table)->getColumn('file_reject') === null) {
                $this->addColumn($table, 'file_reject', $this->text());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260513_033719_add_file_reject_to_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260513_033719_add_file_reject_to_tables cannot be reverted.\n";

        return false;
    }
    */
}
