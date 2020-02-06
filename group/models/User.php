<?php

namespace group\models;

use common\models\UserTrait;
use yii\web\IdentityInterface;

class User extends \common\models\Admin implements IdentityInterface
{
    use UserTrait;

    const STATUS_ACTIVE = 20;

    public static function findGroup()
    {
        return parent::find()->joinWith(['employee e'=>function($q){
            $q->where(['e.status'=>[1,4,5]])->joinWith(['organization o'=>function($qq){
                $qq->where(['o.name'=>['团教部','团课部','团操部']]);
            }],FALSE);
        }],FALSE);
    }

    public static function findIdentity($id)
    {
        return  static::findGroup()->andWhere(['{{%admin}}.id' => $id, '{{%admin}}.status' => self::STATUS_ACTIVE])->one();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findGroup()->andWhere(['accesstoken' => $token])->one();
    }

    public static function findByUsername($username)
    {
        return static::findGroup()->andWhere(['{{%admin}}.username' => $username, '{{%admin}}.status' => self::STATUS_ACTIVE])->one();
    }

    public static function findByMobile($mobile)
    {
        return static::findGroup()->andWhere(['e.mobile'=>$mobile, '{{%admin}}.status' => self::STATUS_ACTIVE])->one();
    }
}