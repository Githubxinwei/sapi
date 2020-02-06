<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-customer',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'customer\controllers',
    'components' => [
        'cache' => [
            'keyPrefix'=>'customer_',
        ],

        'request' => [
            'baseUrl' => '/customer',
            'csrfParam' => '_csrf-customer',
        ],

        'user' => [
            'identityClass' => 'customer\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-customer', 'httpOnly' => true],
        ],

        'session' => [
            'name' => 'advanced-customer',
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
            ],
        ]
    ],
    'params' => $params,
];
