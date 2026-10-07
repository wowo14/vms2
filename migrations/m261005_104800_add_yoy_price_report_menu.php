<?php

use yii\db\Migration;

class m261005_104800_add_yoy_price_report_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Find the Report menu parent ID
        $reportMenu = $this->db->createCommand("SELECT id FROM menu WHERE route LIKE '%laporan%' AND parent IS NULL LIMIT 1")->queryOne();
        
        $parentId = $reportMenu ? $reportMenu['id'] : null;
        
        // If no Report menu found, create one
        if (!$parentId) {
            $this->insert('menu', [
                'name' => 'LAPORAN',
                'parent' => null,
                'route' => '/report/index',
                'icon' => 'fa fa-chart-bar',
                'order' => 20,
            ]);
            $parentId = $this->db->getLastInsertID();
        }

        // Get current max order for children of Report menu
        $maxOrder = $this->db->createCommand("SELECT MAX(`order`) as max_order FROM menu WHERE parent = :parent", [':parent' => $parentId])->queryScalar();
        $nextOrder = $maxOrder ? $maxOrder + 1 : 1;

        // Insert new YoY Price Report menu under Report
        $this->insert('menu', [
            'name' => 'Perbandingan Harga YoY',
            'parent' => $parentId,
            'route' => '/report/perbandingan-harga-yoy',
            'icon' => 'fa fa-chart-line',
            'order' => $nextOrder,
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
        // Delete the YoY Price Report menu
        $this->delete('menu', ['route' => '/report/perbandingan-harga-yoy']);

        // Invalidate menu cache
        if (Yii::$app->cache) {
            \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'tag_menu');
        }
    }
}
