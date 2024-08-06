<?php
use yii\db\Migration;
class m240806_021851_tbl_negosiasi extends Migration
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
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime(),
        ]);
    }
    public function safeDown()
    {
        echo "m240806_021851_tbl_negosiasi cannot be reverted.\n";
        return false;
    }
}
