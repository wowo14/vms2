<?php
$params = require(__DIR__ . '/params.php');
return [
    'id' => 'minimal-console',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'Asia/Jakarta',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@console'   => '@runtime',
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationNamespaces' => [
                'nemmo\attachments\migrations',
            ],
        ],
        'dump' => [
            'class' => 'hzhihua\dump\DumpController',
            // 'db' => 'db', // Connection
            // 'templateFile' => '@vendor/hzhihua/yii2-dump/templates/migration.php',
            'generateFilePath' => '@console/migrations',
            // 'table' => 'table1,table2', // select which table will be dump(default filter migration table)
            // 'filter' => 'table3,table4', // table3 and table4 will be filtered when generating migration file
            'limit' => '0,10', // select * from tableName limit 0,1000
            'filePrefix' => 'Dump',
            'tableOptions' => 'ENGINE=InnoDB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci”', // if mysql >= 5.7, you can set “ENGINE=InnoDB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci”,
        ],
    ],
    'modules'=>[
        'attachments' => [
            'class' => nemmo\attachments\Module::class,
            'tempPath' => '@app/uploads/temp',
            'storePath' => '@app/uploads/store',
            'rules' => [ // Rules according to the FileValidator
                'maxFiles' => 10, // Allow to upload maximum 3 files, default to 3
                'mimeTypes' => 'image/png', // Only png images
                'maxSize' => 1024 * 1024 // 1 MB
            ],
            'tableName' => '{{%attachments}}' // Optional, default to 'attach_file'
        ],
    ],
    'components' => [
        'tools' => [
            'class' => 'app\widgets\Tools'
        ],
        'regions' => [
            'class' => 'app\widgets\Apiregion'
        ],
        'regions' => [
            'class' => 'app\widgets\Apiregion'
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
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
        'db' => require(__DIR__ . '/sqliteDb.php'),
        // 'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];
