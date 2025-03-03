<?php

use yii\db\Migration;

class m250303_014348_alterpaketdetails extends Migration
{
    private function rebuildTable($table, $column, $type) {
        $schema = $this->db->getTableSchema($table);
        $columns = [];
        foreach ($schema->columns as $col) {
            if ($col->name === $column) {
                $columns[$col->name] = $type;
            } else {
                $columns[$col->name] = $col->dbType;
            }
        }
        $tempTable = $table . '_temp';
        $this->createTable($tempTable, $columns);
        $this->execute("INSERT INTO $tempTable SELECT * FROM $table");
        $this->dropTable($table);
        $this->renameTable($tempTable, $table);
    }
    public function safeUp()
    {
        $this->rebuildTable('paket_pengadaan_details', 'id', $this->primaryKey());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250303_014348_alterpaketdetails cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250303_014348_alterpaketdetails cannot be reverted.\n";

        return false;
    }
    */
}
