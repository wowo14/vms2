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
        // alter table paket_pengadaan add new columns addition
        $this->addColumn('paket_pengadaan','addition',$this->text());
    }
    public function safeDown()
    {
        echo "m240703_043250_historiReject cannot be reverted.\n";
        return false;
    }
}
