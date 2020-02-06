<?php
/**
 * 需要权限验证的公共控制器类
 */
namespace common\base;

use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

abstract class AuthBaseController extends BaseController
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    HttpBasicAuth::className(),
                    HttpBearerAuth::className(),
                    ['class'=>QueryParamAuth::className(), 'tokenParam' => 'accesstoken'],
                ]
            ]
        ]);
    }

}