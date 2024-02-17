<?php
namespace app\assets;
use yii\web\AssetBundle;
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'js/autoNumeric.js',
        'js/app.js',
        // 'js/pdf.min.js',
        // 'https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        // 'yii\bootstrap\BootstrapAsset',
        // 'https://code.jquery.com/jquery-3.4.0.min.js',
        // 'https://code.jquery.com/jquery-3.6.0.min.js',
    ];
}
