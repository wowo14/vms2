<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/sqliteDb.php';

$config = [
    'id' => 'vms2-api',
    'name' => 'VMS2 API',
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
    'components' => [
        'request' => [
            'cookieValidationKey' => 'iaMA-BL8GNJP0pW5vbtvLov3Tchxh_6n',
            'enableCsrfValidation' => false, // API tidak butuh CSRF
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                        'status' => $response->statusCode,
                        'message' => $response->statusText,
                    ];
                }
            },
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false, // API stateless
            'loginUrl' => null,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '' => 'v1/default/index',
                'dpp' => 'v1/dpp/index',
                'dpp/<id>' => 'v1/dpp/view',
                'dpp/create' => 'v1/dpp/create',
                'dpp/update/<id>' => 'v1/dpp/update',
                'dpp/delete/<id>' => 'v1/dpp/delete',
                
                // Paket Pengadaan routes
                'paket' => 'v1/paket/index',
                'paket/<id>' => 'v1/paket/view',
                
                // Penawaran routes
                'penawaran' => 'v1/penawaran/index',
                'penawaran/<id>' => 'v1/penawaran/view',
                'penawaran/submit' => 'v1/penawaran/submit',
                
                // Penyedia routes
                'penyedia' => 'v1/penyedia/index',
                'penyedia/<id>' => 'v1/penyedia/view',
                
                // Auth routes
                'auth/login' => 'v1/auth/login',
                'auth/logout' => 'v1/auth/logout',
                'auth/refresh' => 'v1/auth/refresh',
            ],
        ],
        'db' => $db,
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/api.log',
                ],
            ],
        ],
        'apiAuth' => [
            'class' => 'app\components\ApiAuth',
        ],
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\modules\api\v1\Module',
        ],
    ],
    'params' => $params,
];

return $config;