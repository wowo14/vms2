<?php

use yii\db\Migration;

/**
 * Class m240110_065421_tablePenugasanPemilihanpenyedia
 */
class m240110_065421_tablePenugasanPemilihanpenyedia extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //if table exists drop it
        // $this->dropTableIfExists('penugasan_pemilihanpenyedia');
        if ($this->db->schema->getTableSchema('penugasan_pemilihanpenyedia') !== null) {
            $this->dropTable('penugasan_pemilihanpenyedia');
        }
        $this->createTable('penugasan_pemilihanpenyedia', [
            'id' => $this->primaryKey(),
            'dpp_id' => $this->integer(),
            'nomor_tugas'=> $this->string(255),
            'tanggal_tugas'=> $this->date(),
            'pejabat'=> $this->integer(),
            'admin'=>$this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by'=> $this->integer(),
            'updated_by'=> $this->integer(),
        ]);
        //add foreignkey to table dpp for dpp_id
        // $this->addForeignKey('fk-penugasan-dpp_id-dpp','penugasan_pemilihanpenyedia','dpp_id', 'dpp','id','CASCADE','CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240110_065421_tablePenugasanPemilihanpenyedia cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240110_065421_tablePenugasanPemilihanpenyedia cannot be reverted.\n";

        return false;
    }
    */
}
