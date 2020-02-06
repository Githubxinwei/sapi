<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-business',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'business\controllers',
    'components' => [
        'cache' => [
            'keyPrefix'=>'business_',
        ],

        'request' => [
            'baseUrl' => '/business',
            'csrfParam' => '_csrf-business',
        ],

        'user' => [
            'identityClass' => 'business\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-business', 'httpOnly' => true],
        ],

        'session' => [
            'name' => 'advanced-business',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'member'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'organization'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'employee'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'category'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'approval'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'complaint'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'class-comment'],
            ],
        ]
    ],
    'params' => $params,
];
