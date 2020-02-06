<?php
namespace common\models;

use Yii;

trait UserTrait
{
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
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

    /**
     * 返回客户端的信息
     */
    public function getApiInfo()
    {
        \Yii::$app->language == 'en-us'?$msg ='Login success':$msg = '登录成功';
        return [
            'msg'=>$msg,
            'accesstoken' => $this->accesstoken,
        ];
    }
}