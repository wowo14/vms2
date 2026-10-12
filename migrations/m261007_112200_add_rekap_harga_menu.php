<?php

use yii\db\Migration;

/**
 * Handles adding menu item for Rekap Perbandingan Harga.
 */
class m261007_112200_add_rekap_harga_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Find the Report menu parent ID
        $reportMenu = $this->db->createCommand("SELECT id FROM menu WHERE name LIKE '%laporan%' AND parent IS NULL LIMIT 1")->queryOne();
        
        $parentId = $reportMenu ? $reportMenu['id'] : null;
        
        // Create parent menu if not exists
        if (!$parentId) {
            $this->insert('menu', [
                'name' => 'LAPORAN',
                'parent' => null,
                'route' => '/report/index',
                'icon' => 'fa fa-chart-bar',
                'order' => 10,
            ]);
            $parentId = $this->db->getLastInsertID();
        }

        // Get current max order for children of Report menu
        $maxOrder = $this->db->createCommand("SELECT MAX(`order`) as max_order FROM menu WHERE parent = :parent", [':parent' => $parentId])->queryScalar();
        $nextOrder = $maxOrder ? $maxOrder + 1 : 1;

        // Insert new Rekap Harga menu under Report
        $this->insert('menu', [
            'name' => 'Rekap Perbandingan Harga',
            'parent' => $parentId,
            'route' => '/report/rekap-harga',
            'icon' => 'fa fa-chart-bar',
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
        // Delete the menu item
        $this->delete('menu', ['route' => '/report/rekap-harga']);

        // Invalidate menu cache
        if (Yii::$app->cache) {
            \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'tag_menu');
        }
    }
}
