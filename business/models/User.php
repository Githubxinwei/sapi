<?php

namespace business\models;

use Yii;
use common\models\Func;
use common\models\UserTrait;
use yii\web\IdentityInterface;

class User extends \common\models\Admin implements IdentityInterface
{
    use UserTrait;

    const STATUS_READY = 10;
    const STATUS_EMPLOYEE = 20;
    const STATUS_MANAGER = 30;

    public static function findBusiness()
    {
        return parent::find()->joinWith(['employee e'=>function($q){
            $q->where(['e.status'=>1]);
        }],FALSE);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => [self::STATUS_EMPLOYEE, self::STATUS_MANAGER]]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accesstoken' => $token, 'status' => [self::STATUS_EMPLOYEE, self::STATUS_MANAGER]]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => [self::STATUS_EMPLOYEE, self::STATUS_MANAGER]]);
    }

    public static function findByMobile($mobile)
    {
        return static::find()->alias('a')->joinWith('employee e')->where(['e.mobile'=>$mobile, 'a.status' => [self::STATUS_EMPLOYEE, self::STATUS_MANAGER]])->one();
    }

    public function getApiInfo()
    {
        $pid = $style = '0';

        if(!empty($auth = Func::getRelationVal($this, 'role', 'auth'))){
            $authCompanyIds = array_unique($auth->company_id);
            $authVenueIds = array_unique($auth->venue_id);
            if(count($authCompanyIds) == 1){
                $pid = $authCompanyIds[0];
                $style = '1';
                if(count($authVenueIds) == 1){
                    $pid = $authVenueIds[0];
                    $style = '2';
                }
            }
        }

        return [
            'accesstoken' => $this->accesstoken,
            'pid' => $pid,
            'style' => $style,
            'company_name' => Func::getRelationVal($this, 'employee', 'company', 'name'),
            'venue_name' => Func::getRelationVal($this, 'employee', 'venue', 'name'),
        ];
    }
}