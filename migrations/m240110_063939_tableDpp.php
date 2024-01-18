<?php
use yii\db\Migration;
/**
 * Class m240110_063939_tableDpp
 */
class m240110_063939_tableDpp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //if table exists drop it
        if ($this->db->schema->getTableSchema('dpp') !== null) {
            $this->dropTable('dpp');
        }
        $this->createTable('dpp', [
            'id' => $this->primaryKey(),
            'nomor_dpp' => $this->string(255),
            'tanggal_dpp'=> $this->date(),
            'bidang_bagian'=> $this->string(255),
            'paket_id'=> $this->integer(),
            'status_review'=>$this->tinyInteger(),
            'is_approved'=>$this->tinyInteger(),
            'nomor_persetujuan'=>$this->string(255),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by'=> $this->integer(),
            'updated_by'=> $this->integer(),
        ]);
        //add foreignkey to table paket_pengadaan and dpp
        // $this->addForeignKey('fk-dpp-paket_id-paket-pengadaan','dpp','paket_id', 'paket_pengadaan','id','CASCADE','CASCADE');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240110_063939_tableDpp cannot be reverted.\n";
        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    }
    public function down()
    {
        echo "m240110_063939_tableDpp cannot be reverted.\n";
        return false;
    }
    */
}
