<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'components' => [
        'push' => [
            'class' => 'xing\push\Yii',
            'driveName' => 'GeTui', // 驱动名
            // 配置
            'config' => [
                'AppID' => 'BRGv3piGCe8ZzWHAKh1J92',
                'AppKey' => 'piHh7iES3W6WRBSIc738M1',
                'MasterSecret' => '0mkR1SiqD67cGlXsIvRU23',
            ]
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
// 个推配置

        'response' => [
            'class' => 'yii\web\Response',
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if(!isset($response->data['code'])){
                    $message = '';
                    if(isset($response->data['message'])){
                        $message = $response->data['message'];
                    }elseif(!$response->isSuccessful && isset($response->data[0]['message'])){
                        $message = $response->data[0]['message'];
                    }
                    $response->data = [
                        'message' => $message,
                        'code' => $response->isSuccessful ? 1 : 0,
                        'status' => $response->statusCode,
                        'data' => $response->data ?: [],
                    ];
                }
                $response->statusCode = 200;
            },
        ],

        'cache' => [
            'class' => 'yii\caching\DbCache',
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
];
