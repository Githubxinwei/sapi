<?php

namespace service\models;

use Yii;
use common\models\Func;
use common\models\UserTrait;
use yii\web\IdentityInterface;
use common\models\relations\AdminRelations;

class User extends \common\models\Admin implements IdentityInterface
{
    use AdminRelations;

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
    public static function tableName()
    {
        return '{{%admin}}';
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
            if (isset($auth->company_id)){
                $authCompanyIds = array_unique($auth->company_id);
            }
            if (isset($auth->venue_id)){
                $authVenueIds = array_unique($auth->venue_id);
            }
            if (isset($authCompanyIds)){
                if(count($authCompanyIds) == 1){
                    $pid = $authCompanyIds[0];
                    $style = '1';
                    if(count($authVenueIds) == 1){
                        $pid = $authVenueIds[0];
                        $style = '2';
                    }
                }
            }else{
                $pid = null;
                $style = null;
            }


        }
        return [
            'accesstoken' => $this->accesstoken,
            'pid' => $pid,
            'pic'=>Func::getRelationVal($this, 'employee',  'pic'),
            'style' => $style,
            'company_name' => Func::getRelationVal($this, 'employee', 'company', 'name'),
            'venue_name' => Func::getRelationVal($this, 'employee', 'venue', 'name'),
            'venue_id' => Func::getRelationVal($this, 'employee', 'venue', 'id'),
            'organization_name'=> Func::getRelationVal($this, 'employee','organization', 'name'),
        ];
    }
    public function getIsManager()
    {
        return isset($this->role->name) && strpos($this->role->name, '私教') !== FALSE && strpos($this->role->name, '经理') !== FALSE;
    }
}