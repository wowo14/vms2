<?php

use yii\db\Migration;

class m260225_063251_create_minikompetisi_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Table minikompetisi
        $this->createTable('{{%minikompetisi}}', [
            'id' => $this->primaryKey(),
            'judul' => $this->string(255)->notNull(),
            'tanggal' => $this->date(),
            // 1: Harga Terendah, 2: Kualitas & Harga, 3: Lumpsum
            'metode' => $this->integer()->notNull()->defaultValue(1),
            'bobot_kualitas' => $this->decimal(5, 2)->defaultValue(0),
            'bobot_harga' => $this->decimal(5, 2)->defaultValue(0),
            'status' => $this->integer()->defaultValue(0), // 0: draft, 1: published, 2: completed
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_by' => $this->integer(),
        ], $tableOptions);

        // Table minikompetisi_item (Produk yang dibutuhkan)
        $this->createTable('{{%minikompetisi_item}}', [
            'id' => $this->primaryKey(),
            'minikompetisi_id' => $this->integer()->notNull(),
            'nama_produk' => $this->string(255)->notNull(),
            'qty' => $this->decimal(10, 2)->notNull(),
            'satuan' => $this->string(50),
            'harga_hps' => $this->decimal(15, 2),
            'harga_existing' => $this->decimal(15, 2),
        ], $tableOptions);

        // Table minikompetisi_vendor (Vendor yang diundang)
        $this->createTable('{{%minikompetisi_vendor}}', [
            'id' => $this->primaryKey(),
            'minikompetisi_id' => $this->integer()->notNull(),
            'nama_vendor' => $this->string(255)->notNull(),
            'email_vendor' => $this->string(255),
        ], $tableOptions);

        // Table minikompetisi_penawaran (Konsolidasi penawaran per vendor)
        $this->createTable('{{%minikompetisi_penawaran}}', [
            'id' => $this->primaryKey(),
            'minikompetisi_id' => $this->integer()->notNull(),
            'vendor_id' => $this->integer()->notNull(),
            'total_harga' => $this->decimal(15, 2)->defaultValue(0),
            'total_skor_kualitas' => $this->decimal(8, 2)->defaultValue(0),
            'total_skor_harga' => $this->decimal(8, 2)->defaultValue(0),
            'total_skor_akhir' => $this->decimal(8, 2)->defaultValue(0),
            'ranking' => $this->integer(),
            'is_winner' => $this->boolean()->defaultValue(false),
            'created_at' => $this->dateTime(),
        ], $tableOptions);

        // Table minikompetisi_penawaran_item (Detail penawaran per item)
        $this->createTable('{{%minikompetisi_penawaran_item}}', [
            'id' => $this->primaryKey(),
            'penawaran_id' => $this->integer()->notNull(),
            'item_id' => $this->integer()->notNull(),
            'harga_penawaran' => $this->decimal(15, 2)->defaultValue(0),
            'skor_kualitas' => $this->decimal(8, 2)->defaultValue(0),
            'keterangan' => $this->text(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%minikompetisi_penawaran_item}}');
        $this->dropTable('{{%minikompetisi_penawaran}}');
        $this->dropTable('{{%minikompetisi_vendor}}');
        $this->dropTable('{{%minikompetisi_item}}');
        $this->dropTable('{{%minikompetisi}}');
    }
}
