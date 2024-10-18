<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css',
        'https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css',
    ];
    public $js = [
        'js/autoNumeric.js',
        'js/app.js',
        // 'js/pdf.min.js',
        'https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js',
        'https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js',
        'https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js',
        'https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        // 'yii\bootstrap\BootstrapAsset',
        // 'https://code.jquery.com/jquery-3.4.0.min.js',
        // 'https://code.jquery.com/jquery-3.6.0.min.js',
    ];
}
