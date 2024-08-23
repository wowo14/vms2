<?php
use yii\db\Migration;
class m240821_021025_alter_paketp extends Migration
{
    public function safeUp()
    {
        $this->addColumn('paket_pengadaan', 'nomor_persetujuan', $this->text());
        $this->addColumn('paket_pengadaan', 'tanggal_dpp', $this->text());
        $this->addColumn('paket_pengadaan', 'tanggal_persetujuan', $this->text());
    }

}
