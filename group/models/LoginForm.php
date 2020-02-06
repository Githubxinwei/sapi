<?php
namespace group\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $mobile;
    public $password;
    private $_user;
    private $language;

    public function rules()
    {
        return [
            [['mobile', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }
    public function __construct($language)
    {
       $this->language = $language;
    }

    public function  attributeLabels()
    {
        if ($this->language == 'zh-CN'){
            return [
                'mobile'   => '手机号码',
                'password' => '密码',
            ];
        }else{
            return [
                'mobile'   => 'mobile',
                'password' => 'password',
            ];
        }

    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $msg = $this->language == 'zh-CN' ?'手机号码或密码错误':'Phone number or password error';
                $this->addError($attribute, $msg);
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