<?php
use yii\db\Migration;
class m240828_030850_alter_paket_pengadaan_details_table extends Migration
{
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema('paket_pengadaan_details', true) !== null) {
            // If the table exists, drop it
            $this->dropTable('paket_pengadaan_details');
        }
        $this->createTable('paket_pengadaan_details', [
            'id' => $this->primaryKey(),
            'paket_id' => $this->integer(),
            'nama_produk' => $this->string(255)->notNull(),
            'volume' => $this->integer(),
            'qty' => $this->integer(),
            'satuan' => $this->string(255)->notNull(),
            'hps_satuan' => $this->decimal(10, 2),
            'penawaran' => $this->decimal(10, 2),
            'negosiasi' => $this->decimal(10, 2),
            'durasi' => $this->string(255),
            'informasi_harga' => $this->decimal(10, 2),
            'sumber_informasi' => $this->string(255),
        ]);
    }
    public function safeDown()
    {
        // $this->dropTable('paket_pengadaan_details');
    }
}
