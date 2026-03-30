<?php

use yii\db\Migration;

class m260312_060002_create_product_catalog extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // product_catalog: master normalized product names
        $this->createTable('{{%product_catalog}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull()->defaultValue(1),
            'canonical_name' => $this->string(255)->notNull(),
            'category' => $this->string(100)->null(),
            'sub_category' => $this->string(100)->null(),
            'default_unit' => $this->string(50)->null(),
            'description' => $this->text()->null(),
            'is_active' => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('idx_pc_company', '{{%product_catalog}}', 'company_id');
        $this->createIndex('idx_pc_category', '{{%product_catalog}}', ['company_id', 'category']);
        $this->createIndex('idx_pc_name', '{{%product_catalog}}', ['company_id', 'canonical_name']);

        // product_alias: variant names for fuzzy matching
        $this->createTable('{{%product_alias}}', [
            'id' => $this->primaryKey(),
            'product_catalog_id' => $this->integer()->notNull(),
            'alias_name' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_pa_catalog', '{{%product_alias}}', 'product_catalog_id');
        $this->createIndex('idx_pa_alias', '{{%product_alias}}', 'alias_name');
    }

    public function safeDown()
    {
        $this->dropTable('{{%product_alias}}');
        $this->dropTable('{{%product_catalog}}');
    }
}
