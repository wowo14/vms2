<?php
use yii\db\Migration;
class m240703_043250_historiReject extends Migration
{
    public function safeUp()
    {
        $this->createTable('histori_reject', [
            'id' => $this->primaryKey(),
            'paket_id' => $this->integer()->notNull(),
            'nomor' => $this->string(),
            'nama_paket' => $this->string(),
            'user_id' => $this->integer(),
            'alasan_reject'=>$this->text(),
            'tanggal_reject'=>$this->date(),
            'kesimpulan'=>$this->text(),
            'tanggal_dikembalikan'=>$this->date(),
            'tanggapan_ppk'=>$this->text(),
            'file_tanggapan'=>$this->text(),
            'created_at' => $this->integer(),
        ]);
    }
    public function safeDown()
    {
        echo "m240703_043250_historiReject cannot be reverted.\n";
        return false;
    }
}
