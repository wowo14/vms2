<?php
use yii\db\Migration;/**
 * Class m240110_064323_tableReviewDpp
 */
class m240110_064323_tableReviewDpp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //if table exists drop it
        // $this->dropTableIfExists('review_dpp');
        if ($this->db->schema->getTableSchema('review_dpp') !== null) {
            $this->dropTable('review_dpp');
        }
        $this->createTable('review_dpp', [
            'id' => $this->primaryKey(),
            'dpp_id' => $this->integer(),
            'tanggal_review'=> $this->date(),
            'pejabat'=> $this->integer(),
            'uraian'=> $this->text(),
            'kesesuaian'=>$this->tinyInteger(),
            'keterangan'=>$this->string(255),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by'=> $this->integer(),
            'updated_by'=> $this->integer(),
        ]);
        //add foreignkey to table dpp
        // $this->addForeignKey('fk-review_dpp-dpp_id-dpp','review_dpp','dpp_id', 'dpp','id','CASCADE','CASCADE');
    }    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240110_064323_tableReviewDpp cannot be reverted.\n";        return false;
    }    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {    }    public function down()
    {
        echo "m240110_064323_tableReviewDpp cannot be reverted.\n";        return false;
    }
    */
}
