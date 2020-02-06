<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-group',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'group\controllers',
    'components' => [
        'cache' => [
            'keyPrefix'=>'group_',
        ],
        'request' => [
            'baseUrl' => '/group',
            'csrfParam' => '_csrf-group',
        ],

        'user' => [
            'identityClass' => 'group\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-group', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'advanced-group',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => ['member', 'group-class']],
            ],
        ]
    ],
    'params' => $params,
];
