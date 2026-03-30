<?php

use yii\db\Migration;

class m260312_060004_create_pricing_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        // pricing_dataset: analytics data warehouse
        $this->createTable('{{%pricing_dataset}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull()->defaultValue(1),
            'fiscal_year' => $this->integer(4)->null(),
            'procurement_date' => $this->date()->notNull(),
            'procurement_id' => $this->integer()->notNull(),
            'quotation_version_id' => $this->integer()->notNull(),
            'vendor_id' => $this->integer()->notNull(),
            'product_catalog_id' => $this->integer()->null(),
            // Denormalized for analytics independence
            'product_raw_name' => $this->string(255)->notNull(),
            'product_norm_name' => $this->string(255)->null(),
            'product_category' => $this->string(100)->null(),
            'specification' => $this->text()->null(),
            'location' => $this->string(100)->null(),
            // Measures
            'unit' => $this->string(50)->null(),
            'quantity' => $this->decimal(14, 4)->notNull(),
            'unit_price' => $this->decimal(18, 2)->notNull(),
            'total_price' => $this->decimal(18, 2)->notNull(),
            'hps_price' => $this->decimal(18, 2)->null(),
            'price_ratio_vs_hps' => $this->decimal(8, 4)->null(),
            'is_winner' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'version_number' => $this->smallInteger()->notNull()->defaultValue(1),
            'is_latest_version' => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'ingested_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('idx_pd_company_date', '{{%pricing_dataset}}', ['company_id', 'procurement_date']);
        $this->createIndex('idx_pd_product_norm', '{{%pricing_dataset}}', ['company_id', 'product_norm_name']);
        $this->createIndex('idx_pd_vendor', '{{%pricing_dataset}}', ['company_id', 'vendor_id']);
        $this->createIndex('idx_pd_catalog', '{{%pricing_dataset}}', 'product_catalog_id');
        $this->createIndex('idx_pd_winner', '{{%pricing_dataset}}', ['company_id', 'is_winner']);
        $this->createIndex('idx_pd_fiscal', '{{%pricing_dataset}}', ['company_id', 'fiscal_year']);
        $this->createIndex('idx_pd_mk_vendor', '{{%pricing_dataset}}', ['procurement_id', 'vendor_id']);

        // vendor_price_index: competitiveness analytics (refreshed periodically)
        $this->createTable('{{%vendor_price_index}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull()->defaultValue(1),
            'vendor_id' => $this->integer()->notNull(),
            'product_catalog_id' => $this->integer()->null(),
            'product_category' => $this->string(100)->null(),
            'fiscal_year' => $this->integer(4)->null(),
            'total_bids' => $this->integer()->notNull()->defaultValue(0),
            'total_wins' => $this->integer()->notNull()->defaultValue(0),
            'win_rate' => $this->decimal(5, 2)->null(),
            'avg_price_rank' => $this->decimal(6, 2)->null(),
            'avg_price' => $this->decimal(18, 2)->null(),
            'avg_price_vs_hps' => $this->decimal(8, 4)->null(),
            'competitiveness_score' => $this->decimal(8, 2)->null(),
            'last_calculated_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('idx_vpi_company_vendor', '{{%vendor_price_index}}', ['company_id', 'vendor_id']);
        $this->createIndex('idx_vpi_catalog', '{{%vendor_price_index}}', ['company_id', 'product_catalog_id']);
        $this->createIndex('idx_vpi_score', '{{%vendor_price_index}}', ['company_id', 'competitiveness_score']);

        // price_analytics_summary: materialized aggregate for dashboard
        $this->createTable('{{%price_analytics_summary}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull()->defaultValue(1),
            'product_catalog_id' => $this->integer()->null(),
            'product_norm_name' => $this->string(255)->notNull(),
            'product_category' => $this->string(100)->null(),
            'fiscal_year' => $this->integer(4)->null(),
            'sample_count' => $this->integer()->notNull()->defaultValue(0),
            'vendor_count' => $this->integer()->notNull()->defaultValue(0),
            'min_price' => $this->decimal(18, 2)->null(),
            'max_price' => $this->decimal(18, 2)->null(),
            'avg_price' => $this->decimal(18, 2)->null(),
            'last_seen_price' => $this->decimal(18, 2)->null(),
            'last_seen_at' => $this->date()->null(),
            'refreshed_at' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('idx_pas_company_product', '{{%price_analytics_summary}}', ['company_id', 'product_norm_name']);
        $this->createIndex('idx_pas_catalog', '{{%price_analytics_summary}}', ['company_id', 'product_catalog_id']);
        $this->createIndex('idx_pas_fiscal', '{{%price_analytics_summary}}', ['company_id', 'fiscal_year']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%price_analytics_summary}}');
        $this->dropTable('{{%vendor_price_index}}');
        $this->dropTable('{{%pricing_dataset}}');
    }
}
