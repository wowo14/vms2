<?php

use yii\db\Migration;

class m260714_070523_add_dpp_menus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Shift root menus starting from order 8 to make room for DPP Farmasi (8) and DPP PPK (9)
        $this->update('menu', ['order' => new \yii\db\Expression('`order` + 2')], ['and', ['parent' => null], ['>=', 'order', 8]]);

        // Insert new DPP Farmasi menu
        $this->insert('menu', [
            'name' => 'DPP Farmasi',
            'parent' => null,
            'route' => '/dpp/farmasi',
            'icon' => 'fa fa-medkit',
            'order' => 8,
        ]);

        // Insert new DPP PPK menu
        $this->insert('menu', [
            'name' => 'DPP PPK',
            'parent' => null,
            'route' => '/dpp/ppk',
            'icon' => 'fa fa-user-tie',
            'order' => 9,
        ]);

        // Invalidate menu cache
        if (Yii::$app->cache) {
            \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'tag_menu');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Delete inserted menus
        $this->delete('menu', ['route' => '/dpp/farmasi']);
        $this->delete('menu', ['route' => '/dpp/ppk']);

        // Revert shifted orders
        $this->update('menu', ['order' => new \yii\db\Expression('`order` - 2')], ['and', ['parent' => null], ['>=', 'order', 10]]);

        // Invalidate menu cache
        if (Yii::$app->cache) {
            \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'tag_menu');
        }
    }
}
