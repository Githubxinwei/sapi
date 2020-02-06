<?php
namespace coach\models;

use common\models\base\MessageCode;
use Yii;
use yii\base\Model;

class LoginCodeForm extends Model
{
    public $mobile;
    public $code;
    private $_user;

    public function rules()
    {
        return [
            [['mobile', 'code'], 'required'],
            ['mobile', 'validateMobile'],
            ['code', 'validateCode'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'mobile' => '手机号码',
            'code'   => '验证码',
        ];
    }

    public function validateMobile($attribute)
    {
        if(!$this->hasErrors()){
            if(!$this->getUser()) $this->addError($attribute, '手机号码不存在');
        }
    }

    public function validateCode($attribute)
    {
        if(!$this->hasErrors()){
            $msgCode = MessageCode::find()->where(['mobile'=>$this->mobile, 'code'=>$this->code])->orderBy('create_at DESC')->one();
            if(!$msgCode){
                $this->addError($attribute, '验证码错误');
            }elseif(time() - $msgCode->create_at > 300000){
                $this->addError($attribute, '验证码已失效');
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