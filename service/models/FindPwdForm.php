<?php
namespace service\models;

use common\models\base\MessageCode;
use yii\base\Model;

class FindPwdForm extends Model
{
    public $mobile;
    public $code;
    public $newpwd;
    private $_user;


    public function rules()
    {
        return [
            [['mobile','code','newpwd'], 'required'],
            ['newpwd', 'string', 'min'=>6],
            ['mobile', 'validateMobile'],
            ['code', 'validateCode'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'mobile' => '手机号码',
            'code'   => '验证码',
            'newpwd' => '新密码',
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

    public function reset()
    {
        if ($this->validate()) {
            $this->_user->setPassword($this->newpwd);
            $this->_user->generateAccessToken();
            $this->_user->save();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function info()
    {
        return $this->_user->getApiInfo();
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByMobile($this->mobile);
        }

        return $this->_user;
    }

}