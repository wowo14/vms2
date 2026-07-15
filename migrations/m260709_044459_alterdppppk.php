<?php

use yii\db\Migration;

class m260709_044459_alterdppppk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('dpp', 'jenis_dpp', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('dpp', 'jenis_dpp');
    }
}
