<?php

use yii\db\Migration;

class m260312_060001_alter_minikompetisi_add_fields extends Migration
{
    public function safeUp()
    {
        // Alter minikompetisi: add company_id, location, fiscal_year
        $this->addColumn('{{%minikompetisi}}', 'company_id', $this->integer()->notNull()->defaultValue(1)->after('id'));
        $this->addColumn('{{%minikompetisi}}', 'location', $this->string(100)->null());
        $this->addColumn('{{%minikompetisi}}', 'fiscal_year', $this->integer(4)->null());

        $this->createIndex('idx_mk_company', '{{%minikompetisi}}', 'company_id');
        $this->createIndex('idx_mk_status', '{{%minikompetisi}}', ['company_id', 'status']);

        // Alter minikompetisi_item: add product_category, specification, product_catalog_id
        $this->addColumn('{{%minikompetisi_item}}', 'product_catalog_id', $this->integer()->null());
        $this->addColumn('{{%minikompetisi_item}}', 'product_category', $this->string(100)->null());
        $this->addColumn('{{%minikompetisi_item}}', 'specification', $this->text()->null());

        $this->createIndex('idx_mki_catalog', '{{%minikompetisi_item}}', 'product_catalog_id');
    }

    public function safeDown()
    {
        $this->dropIndex('idx_mki_catalog', '{{%minikompetisi_item}}');
        $this->dropColumn('{{%minikompetisi_item}}', 'specification');
        $this->dropColumn('{{%minikompetisi_item}}', 'product_category');
        $this->dropColumn('{{%minikompetisi_item}}', 'product_catalog_id');

        $this->dropIndex('idx_mk_status', '{{%minikompetisi}}');
        $this->dropIndex('idx_mk_company', '{{%minikompetisi}}');
        $this->dropColumn('{{%minikompetisi}}', 'fiscal_year');
        $this->dropColumn('{{%minikompetisi}}', 'location');
        $this->dropColumn('{{%minikompetisi}}', 'company_id');
    }
}
