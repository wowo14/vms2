<?php
use yii\db\Migration;
/**
 * Class m240110_064648_tablePersetujuanPengadaan
 */
class m240110_064648_tablePersetujuanPengadaan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //if table exists drop it
        // $this->dropTableIfExists('persetujuan_pengadaan');
        if ($this->db->schema->getTableSchema('persetujuan_pengadaan') !== null) {
            $this->dropTable('persetujuan_pengadaan');
        }
        $this->createTable('persetujuan_pengadaan', [
            'id' => $this->primaryKey(),
            'dpp_id' => $this->integer(),
            'paket_id' => $this->integer(),
            'nomor_persetujuan'=> $this->string(255),
            'tanggal_persetujuan'=> $this->date(),
            'perihal'=> $this->text(),
            'ppkom'=>$this->integer(),
            'kpa'=>$this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by'=> $this->integer(),
            'updated_by'=> $this->integer(),
        ]);
        //add foreignkey to table dpp for dpp_id, to table paket_pengadaan for paket_id
        // $this->addForeignKey('fk-persetujuan-paket_id-paket-pengadaan','persetujuan_pengadaan','paket_id', 'paket_pengadaan','id','CASCADE','CASCADE');
        //add foreignkey to table dpp for dpp_id
        // $this->addForeignKey('fk-persetujuan-dpp_id-dpp','persetujuan_pengadaan','dpp_id', 'dpp','id','CASCADE','CASCADE');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240110_064648_tablePersetujuanPengadaan cannot be reverted.\n";
        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    }
    public function down()
    {
        echo "m240110_064648_tablePersetujuanPengadaan cannot be reverted.\n";
        return false;
    }
    */
}
