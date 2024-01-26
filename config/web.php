<?php
$params = require __DIR__ . '/params.php';
// $db = require __DIR__ . '/db.php';
$db = require __DIR__ . '/sqliteDb.php';
$config = [
    'id' => 'vms2',
    'name' => 'VMS',
    'timeZone' => 'Asia/Jakarta',
    'language' => 'id-ID',
    'sourceLanguage' => 'id-ID',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@uploads' => '@app/web/uploads/',
    ],
    'modules' => [
        
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
            // 'bsVersion' => '4.x',
        ],
        'admin' => [
            'class' => 'mdm\admin\Module',
            'layout' => '@app/views/layouts/left-menu-admin',
            'controllerMap' => [
                'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                    'userClassName' => 'app\models\User',
                    // 'userClassName' => 'mdm\admin\models\User',
                    'idField' => 'id'
                ],
            ],
            'menus' => [
                'assignment' => [
                    'label' => 'Grand Access'
                ],
            ]
        ],
    ],
    'components' => [
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'linkAssets' => false,
            'appendTimestamp' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        'jquery.min.js',
                    ]
                ],
                'yii\bootstrap4\BootstrapAsset' => [
                    'css' => ['css/bootstrap.min.css']
                ],
                'yii\bootstrap4\BootstrapPluginAsset' => [
                    'js' => ['js/bootstrap.min.js']
                ],
            ],
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'name' => 'vms2',
            'cookieParams' => ['httponly' => true, 'lifetime' => 3600 * 4],
            'timeout' => 3600 * 4, //session expire 4jam
            'useCookies' => true,
        ],
        'consoleRunner' => [
            'class' => 'vova07\console\ConsoleRunner',
            'file' => '@app/yii' // or an absolute path to console file
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@app/views'
                ],
            ],
        ],
        'request' => [
            'cookieValidationKey' => 'iaMA-BL8GNJP0pW5vbtvLov3Tchxh_6n',
            'enableCsrfValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'enableAutoLogin' => true,
            'identityClass' => 'app\models\User',
            'loginUrl' => ['site/login'],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'cache' => 'cache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => [
                        // 'info',
                        // 'trace',
                        // 'profile',
                        'error',
                        // 'warning'
                    ],
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                'yii2-ajaxcrud' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2ajaxcrud/ajaxcrud/messages',
                    'sourceLanguage' => 'id-ID',
                ],
            ]
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [],
        ],
        'tools' => [
            'class' => 'app\widgets\Tools'
        ],
        'regions' => [
            'class' => 'app\widgets\Apiregion'
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
            'locale' => 'id_ID',
            'defaultTimeZone' => 'Asia/Jakarta',
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'currencyCode' => 'IDR',
        ],
        'messageformater' => [
            'class' => 'yii\i18n\MessageFormatter',
            'nullDisplay' => '',
            'locale' => 'id_ID',
            'defaultTimeZone' => 'Asia/Jakarta',
        ],
        'pdf' => [
            'class' => 'kartik\mpdf\Pdf',
            'format' => [215, 330], //'A4',
            'orientation' => 'P',
            'marginTop' => 1,
            'marginBottom' => 1,
            'marginHeader' => 1,
            'marginFooter' => 1,
            'marginLeft' => 1,
            'marginRight' => 1,
            'destination' => 'I',
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'admin/*',
            (YII_DEBUG) ? 'debug/*' : '',
            'site/*',
            'site/login',
            'site/error',
        ]
    ],
    'as beforeRequest' => [
        'class' => 'yii\filters\AccessControl',
        'rules' => [
            ['actions' => ['login', 'captcha'], 'allow' => true,],
            ['controllers' => ['monitoringserver'], 'allow' => true,],
            ['allow' => true, 'roles' => ['@'],],
        ],
    ],
    'params' => $params,
];
if (YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [ // here
            'crud' => [ // generator name
                'class' => 'yii\gii\generators\crud\Generator', // generator class
                'templates' => [ // setting for our templates
                    'yii2-adminlte3' => '@vendor/hail812/yii2-adminlte3/src/gii/generators/crud/default' // template name => path to template
                ]
            ]
        ]
    ];
}
return $config;
