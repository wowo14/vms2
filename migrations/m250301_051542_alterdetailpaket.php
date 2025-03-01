<?php
use yii\db\Migration;
class m250301_051542_alterdetailpaket extends Migration
{
    private function rebuildTable($table, $column, $type) {
        $schema = $this->db->getTableSchema($table);
        $columns = [];
        foreach ($schema->columns as $col) {
            if ($col->name === $column) {
                $columns[$col->name] = $type;
            } else {
                $columns[$col->name] = $col->dbType;
            }
        }
        $tempTable = $table . '_temp';
        $this->createTable($tempTable, $columns);
        $this->execute("INSERT INTO $tempTable SELECT * FROM $table");
        $this->dropTable($table);
        $this->renameTable($tempTable, $table);
    }
    public function safeUp()
    {
        //modify column qty and volume in paketpengadaan details to float
        $this->rebuildTable('paket_pengadaan_details', 'qty', $this->float());
        $this->rebuildTable('paket_pengadaan_details', 'volume', $this->float());
    }
    /*
    Name	Type	Size	Scale	Not Null	Key	Default Value	Collate	Not Null ON CONFLICT	Auto Increment
    id	integer			No	true						true
    paket_id	integer			Yes	false						false
    nama_produk	varchar	255		No	false						false
    volume	decimal			Yes	false						false
    qty	decimal			Yes	false						false
    satuan	varchar	255		No	false						false
    hps_satuan	decimal	10	2	Yes	false						false
    penawaran	decimal	10	2	Yes	false						false
    negosiasi	decimal	10	2	Yes	false						false
    durasi	varchar	255		Yes	false						false
    informasi_harga	decimal	10	2	Yes	false						false
    sumber_informasi	varchar	255		Yes	false						false
    */
}
