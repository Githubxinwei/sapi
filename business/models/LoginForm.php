<?php
namespace business\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    private $_user;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'username'   => '用户名',
            'password' => '密码',
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码错误.');
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
            $this->_user = User::findByusername($this->username);
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