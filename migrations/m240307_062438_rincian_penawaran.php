<?php
use yii\db\Migration;
class m240307_062438_rincian_penawaran extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%rincian_penawaran}}', [
            'id' => $this->primaryKey(),
            'penawaran_id' => $this->integer()->notNull(),
            'paket_id' => $this->integer()->notNull(),
            'nama_produk' => $this->string(255)->notNull(),
            'satuan' => $this->string(50),
            'volume' => $this->string(50),
            'harga_satuan'=> $this->double(),
            'total_sebelum_pajak'=> $this->double(),
            'pajak'=> $this->double(),
            'total_setelah_pajak'=> $this->double(),
            'keterangan'=>$this->text(),
            'pdn'=>$this->integer(),
        ]);
    }
    public function safeDown()
    {
        echo "m240307_062438_rincian_penawaran cannot be reverted.\n";
        return false;
    }
}
