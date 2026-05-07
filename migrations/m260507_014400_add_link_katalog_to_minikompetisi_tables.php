<?php

use yii\db\Migration;

/**
 * Class m260507_014400_add_link_katalog_to_minikompetisi_tables
 */
class m260507_014400_add_link_katalog_to_minikompetisi_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%minikompetisi_item}}', 'link_katalog', $this->text());
        $this->addColumn('{{%minikompetisi_penawaran_item}}', 'link_katalog', $this->text());
        $this->addColumn('{{%quotation_item}}', 'link_katalog', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%minikompetisi_item}}', 'link_katalog');
        $this->dropColumn('{{%minikompetisi_penawaran_item}}', 'link_katalog');
        $this->dropColumn('{{%quotation_item}}', 'link_katalog');
    }
}
