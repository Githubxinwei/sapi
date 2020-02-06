<?php
namespace customer\controllers;

use common\models\Func;
use Yii;
use common\base\AuthBaseController;
use common\models\Member;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class SwitchController extends AuthBaseController
{
    public function actionVenue($venue_id)
    {
        $user = Yii::$app->user->identity;
        $venue_ids = ArrayHelper::getColumn(Member::find()->select('venue_id')->where(['member_account_id'=>$user->id])->asArray()->all(), 'venue_id');
        if(!in_array($venue_id, $venue_ids)) throw new NotFoundHttpException('无此场馆权限');
        Yii::$app->cache->set("account{$user->id}", Member::findOne(['venue_id'=>$venue_id, 'member_account_id'=>$user->id])->id);
    }
}