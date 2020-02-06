<?php
namespace coach\models;

use common\models\relations\AdminRelations;
use Yii;
use yii\web\IdentityInterface;

class User extends \common\models\Admin implements IdentityInterface
{
    use AdminRelations;
    const STATUS_ACTIVE    = 20;

    public static function tableName()
    {
        return '{{%admin}}';
    }

    /**
     * 代替find()方法，查找私教用户
     * @author zhangxiaobing <zhangxiaobing@itsports.club>
     * @create 2017/12/08
     * @return $this
     */
    public static function findCoach()
    {
        return parent::find()->joinWith(['employee e'=>function($q){
            $q->where(['e.status'=>1])->joinWith(['organization o'=>function($qq){
                $qq->where(['o.name'=>'私教部']);
            }],FALSE);
        }],FALSE);
    }

    public static function findIdentity($id)
    {
        return  static::findCoach()->where(['{{%admin}}.id' => $id, '{{%admin}}.status' => self::STATUS_ACTIVE])->asArray()->one();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findCoach()->where(['accesstoken' => $token])->one();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return true;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function findByMobile($mobile)
    {
        return static::findCoach()->where(['e.mobile'=>$mobile, '{{%admin}}.status' => self::STATUS_ACTIVE])->one();
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * accesstoken过期才更新
     */
    public function updateAccessToken()
    {
        if(!$this->isAccessTokenValid()){
            $this->generateAccessToken();
            $this->save();
        }
    }

    public function generateAccessToken($expireInSeconds=24*3600*10)
    {
        $this->accesstoken = Yii::$app->security->generateRandomString() . '_' . (time() + $expireInSeconds);
    }

    /**
     * accesstoken是否已过期
     * @return bool
     */
    public function isAccessTokenValid()
    {
        if (!empty($this->accesstoken)) {
            $timestamp = (int) substr($this->accesstoken, strrpos($this->accesstoken, '_') + 1);
            return $timestamp > time();
        }
        return false;
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * 是否私教经理或总经理
     */
    public function getIsManager()
    {
        return isset($this->role->name) && strpos($this->role->name, '私教') !== FALSE && strpos($this->role->name, '经理') !== FALSE;
    }

    /**
     * 返回客户端的信息
     */
    public function getApiInfo()
    {
        return [
            'accesstoken' => $this->accesstoken,
            'manage'      => $this->getIsManager(),
        ];
    }

}