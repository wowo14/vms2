<?php
use yii\db\Migration;
class m240918_032438_aleter_negosiasi extends Migration
{
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('negosiasi') !== null) {
            $this->dropTable('negosiasi');
        }
        $this->createTable('negosiasi',[
            'id' => $this->primaryKey(),
            'penawaran_id' => $this->integer()->notNull(),
            'ammount'=> $this->double()->notNull(),
            'accept'=> $this->integer(),
            'pp_accept'=> $this->integer(),
            'penyedia_accept'=> $this->integer(),
            'detail' => $this->text(),
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime(),
        ]);
    }
    public function safeDown()
    {
        if ($this->db->schema->getTableSchema('negosiasi') !== null) {
            $this->dropTable('negosiasi');
        }
    }
}
