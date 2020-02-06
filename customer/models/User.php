<?php

namespace customer\models;

use Yii;
use common\models\UserTrait;
use yii\web\IdentityInterface;

class User extends \common\models\MemberAccount implements IdentityInterface
{
    use UserTrait;

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accesstoken' => $token]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}