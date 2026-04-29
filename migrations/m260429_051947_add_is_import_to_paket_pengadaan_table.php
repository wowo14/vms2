<?php

use yii\db\Migration;

/**
 * Class m260429_051947_add_is_import_to_paket_pengadaan_table
 */
class m260429_051947_add_is_import_to_paket_pengadaan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Try adding the column but ignore if it already exists (from my previous manual SQLite alter)
        try {
            $this->addColumn('{{%paket_pengadaan}}', 'is_import', $this->integer()->defaultValue(0));
        } catch (\Exception $e) {
            // Column probably already exists
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%paket_pengadaan}}', 'is_import');
    }
}
