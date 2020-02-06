<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-service',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'service\controllers',
    'components' => [
        'cache' => [
            'keyPrefix'=>'service_',
        ],

        'request' => [
            'baseUrl' => '/service',
            'csrfParam' => '_csrf-service',
        ],

        'user' => [
            'identityClass' => 'service\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-service', 'httpOnly' => true],
        ],

        'session' => [
            'name' => 'advanced-service',
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

            ],
        ]
    ],
    'params' => $params,
];
