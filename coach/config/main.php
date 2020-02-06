<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-coach',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'coach\controllers',
    'components' => [
        'cache' => [
            'keyPrefix'=>'coach_',
        ],

        'request' => [
            'baseUrl' => '/coach',
            'csrfParam' => '_csrf-coach',
        ],

        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-coach', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'advanced-coach',
        ],
    ],
    'params' => $params,
];
