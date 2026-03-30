<?php

use yii\db\Migration;

class m260312_060003_create_quotation_version extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // quotation_version: replaces minikompetisi_penawaran with versioning
        $this->createTable('{{%quotation_version}}', [
            'id' => $this->primaryKey(),
            'minikompetisi_id' => $this->integer()->notNull(),
            'vendor_id' => $this->integer()->notNull(),
            'version_number' => $this->smallInteger()->notNull()->defaultValue(1),
            'version_label' => $this->string(50)->null(),
            'revision_note' => $this->text()->null(),
            'quotation_file_path' => $this->string(500)->null(),
            // 0=draft, 1=submitted, 2=accepted, 3=rejected
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'is_latest' => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'uploaded_at' => $this->dateTime()->null(),
            'uploaded_by' => $this->integer()->null(),
            // Cached aggregate scores
            'total_harga' => $this->decimal(18, 2)->null(),
            'total_skor_kualitas' => $this->decimal(8, 2)->null(),
            'total_skor_harga' => $this->decimal(8, 2)->null(),
            'total_skor_akhir' => $this->decimal(8, 2)->null(),
            'ranking' => $this->smallInteger()->null(),
            'is_winner' => $this->tinyInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('idx_qv_mk_vendor', '{{%quotation_version}}', ['minikompetisi_id', 'vendor_id']);
        $this->createIndex('idx_qv_mk_latest', '{{%quotation_version}}', ['minikompetisi_id', 'is_latest']);
        $this->createIndex('idx_qv_vendor', '{{%quotation_version}}', 'vendor_id');
        $this->createIndex('uq_qv_version', '{{%quotation_version}}', ['minikompetisi_id', 'vendor_id', 'version_number'], true);

        // quotation_item: structured line items per version
        $this->createTable('{{%quotation_item}}', [
            'id' => $this->primaryKey(),
            'quotation_version_id' => $this->integer()->notNull(),
            'minikompetisi_item_id' => $this->integer()->notNull(),
            'product_catalog_id' => $this->integer()->null(),
            'product_name' => $this->string(255)->notNull(),
            'product_name_norm' => $this->string(255)->null(),
            'product_category' => $this->string(100)->null(),
            'specification' => $this->text()->null(),
            'unit' => $this->string(50)->null(),
            'quantity' => $this->decimal(14, 4)->notNull(),
            'unit_price' => $this->decimal(18, 2)->notNull(),
            // total_price stored (cannot use GENERATED ALWAYS in SQLite)
            'total_price' => $this->decimal(18, 2)->null(),
            'skor_kualitas' => $this->decimal(8, 2)->null(),
            'keterangan' => $this->text()->null(),
        ], $tableOptions);

        $this->createIndex('idx_qi_version', '{{%quotation_item}}', 'quotation_version_id');
        $this->createIndex('idx_qi_catalog', '{{%quotation_item}}', 'product_catalog_id');
        $this->createIndex('idx_qi_name_norm', '{{%quotation_item}}', 'product_name_norm');
    }

    public function safeDown()
    {
        $this->dropTable('{{%quotation_item}}');
        $this->dropTable('{{%quotation_version}}');
    }
}
