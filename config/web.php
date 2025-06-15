<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'seed-management',
    'name' => 'System Zarządzania Nasionami',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'dashboard/index',
    'language' => 'pl-PL',
    'timeZone' => 'Europe/Warsaw',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'wstaw-tutaj-swoj-sekretny-klucz-o-dlugosci-minimum-32-znakow',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'dashboard/index',
                'login' => 'site/login',
                'logout' => 'site/logout',
                'about' => 'site/about',
                'change-password' => 'site/change-password',
                'reset-password' => 'site/request-password-reset',
                
                'dashboard' => 'dashboard/index',
                'dashboard/sowing-pdf' => 'dashboard/sowing-pdf',
                'dashboard/germination' => 'dashboard/update-germination',
                'dashboard/labels' => 'dashboard/print-labels',
                'dashboard/report' => 'dashboard/germination-report',
                'dashboard/calendar' => 'dashboard/sowing-calendar',
                
                'seeds' => 'seed/index',
                'seed/create' => 'seed/create',
                'seed/<id:\d+>' => 'seed/view',
                'seed/<id:\d+>/edit' => 'seed/update',
                'seed/<id:\d+>/delete' => 'seed/delete',
                'seed/<id:\d+>/copy' => 'seed/copy',
                'seed/search' => 'seed/search',
                'seed/export' => 'seed/export',
                'seed/stats' => 'seed/stats',
                
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'pl-PL',
            'timeZone' => 'Europe/Warsaw',
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'dd.MM.yyyy HH:mm',
            'timeFormat' => 'HH:mm',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'PLN',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];

return $config;