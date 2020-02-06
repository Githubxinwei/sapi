<?php
namespace coach\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $mobile;
    public $password;
    private $_user;

    public function rules()
    {
        return [
            [['mobile', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'mobile'   => '手机号码',
            'password' => '密码',
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if(!$user){
                $this->addError($attribute, '手机号码不存在');
                return FALSE;
            }
            if(!$user->validatePassword($this->password)){
                $this->addError($attribute, '密码错误');
                return FALSE;
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            Yii::$app->user->login($this->_user);
            Yii::$app->user->identity->updateAccessToken();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByMobile($this->mobile);
        }

        return $this->_user;
    }

    /**
     * 登录成功后返回客户端的会员信息
     * @return array
     */
    public function info()
    {
        return $this->_user->getApiInfo();
    }
}