<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'fa-IR',
    'timezone' => 'Asia/Tehran',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '4FngcoOtV1sOuqKYL80*Y79gcr30e[du9u80kKbbbzuuOnPDGV',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'enableCookieValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\users\TblUsers',
            'enableAutoLogin' => false,
            'enableSession' => true,
            'loginUrl' => null
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure transport
            // for the mailer to send real emails.
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
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'user',
                    'extraPatterns' => [
                        'login' => 'login',
                        'POST change-password' => 'change-password',
                        'OPTIONS change-password' => 'options',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'zone',
                    'extraPatterns' => [
                        'GET all' => 'all',
                        'OPTIONS all' => 'options',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'pager',
                    'extraPatterns' => [
                        'GET all' => 'all',
                        'OPTIONS all' => 'options',

                        'GET pager-status' => 'pager-status',
                        'OPTIONS pager-status' => 'options',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'sound',
                    'extraPatterns' => [
                        'GET all' => 'all',
                        'OPTIONS all' => 'options',
                        /************/
                        'POST upload' => 'upload',
                        'OPTIONS upload' => 'options',
                        /************/
                        'GET max-upload-size' => 'max-upload-size',
                        'OPTIONS max-upload-size' => 'options',
                        /************/
                        'POST test' => 'test',
                        'OPTIONS test' => 'options',
                        /************/
                        'POST hangup' => 'hangup',
                        'OPTIONS hangup' => 'options',

                        'GET get-sounds' => 'get-sounds',
                        'OPTIONS get-sounds' => 'options',

                        'DELETE delete-sounds' => 'delete-sounds',
                        'OPTIONS delete-sounds' => 'options',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller'  => 'manual-play',
                    'extraPatterns' => [
                        'POST play' => 'play',
                        'OPTIONS play' => 'options',
                        /************/
                        'GET all-zones-and-sounds' => 'all-zones-and-sounds',
                        'OPTIONS all-zones-and-sounds' => 'options',

                        'POST hangup-all' => 'hangup-all',
                        'OPTIONS hangup-all' => 'options',

                        "GET get-cur-date-time" => "get-cur-date-time",
                        "OPTIONS get-cur-date-time" => "options",
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'schedule',
                    'extraPatterns' => []
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'prayer',
                    'extraPatterns' => [
                        'GET cur-date' => 'cur-date',
                        'OPTIONS cur-date' => 'options',

                        'POST upload' => 'upload',
                        'OPTIONS upload' => 'options',

                        'POST save-csv' => 'save-csv',
                        'OPTIONS save-csv' => 'options',
                    ]
                ],

            ],
        ],
        'jwt' => [
            'class' => \bizley\jwt\Jwt::class,
            'signer' => \bizley\jwt\Jwt::HS256,
            'signingKey' => "JYET#8635e3^%E73te8737e37e8",
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;