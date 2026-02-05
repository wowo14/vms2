<?php
use app\models\MenuHelper;
use yii\helpers\{Url, ArrayHelper};
?>
<aside class="main-sidebar sidebar-dark-navy elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link">
        <img src="/images/logoupbj.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light"><?= \Yii::$app->name ?></span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= $assetDir ?>/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= \Yii::$app->user->identity->username ?></a>
            </div>
        </div>
        <!-- SidebarSearch Form -->
        <!-- href be escaped -->
        <!-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div> -->
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?php
            $menu = [
                'items' => [
                    // [
                    //     'label' => 'Starter Pages',
                    //     'icon' => 'tachometer-alt',
                    //     'badge' => '<span class="right badge badge-info">2</span>',
                    //     'items' => [
                    //         ['label' => 'Active Page', 'url' => ['site/index'],
                    //         'iconStyle' => 'far'
                    //         ],
                    //     ]
                    // ],
                    ['label' => 'Login', 'url' => ['site/login'], 'icon' => 'sign-in-alt', 'visible' => Yii::$app->user->isGuest],
                    ['label' => 'Logout', 'url' => ['site/logout'], 'icon' => 'sign-in-alt', 'visible' => (!Yii::$app->user->isGuest)]
                ]
            ];
            // (Yii::$app->user->isGuest) ?ArrayHelper::merge($menu, ['items'=>MenuHelper::getAssignedMenu(Yii::$app->user->id)]) : $menu;
            $menu = ArrayHelper::merge(['items' => MenuHelper::getAssignedMenu(Yii::$app->user->id)], $menu);
            // $menu['items'][] = ['label' => 'Report Penyedia', 'url' => ['/report-penyedia/index'], 'icon' => 'file-alt'];
            $menu = collect($menu['items'])->map(function ($item) {
                if (isset($item['url']))
                    if ($item['url'] == ['site/logout'])
                        $item['badge'] = '<span class="right badge badge-danger">' . Yii::$app->user->identity->username . '</span>';
                return $item;
            });
            $menux = ['items' => $menu->toArray()];
            echo \hail812\adminlte\widgets\Menu::widget($menux);
            ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>