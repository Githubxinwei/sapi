<?php
namespace business\models;

use common\models\base\MessageCode;
use common\models\Func;
use common\models\Qiniu;
use yii\base\Model;

class UserUpdateForm extends Model
{
    public $signature;
    public $sex;
    public $name;
    public $mobile;
    public $code;
    public $newpwd;
    public $pic;
    private $_user;
    private $_employee;

    public function __construct(array $config = [])
    {
        $this->_user = User::findOne($config['user_id']);
        $this->_employee = \common\models\Employee::findOne($config['employee_id']);
    }

    public function rules()
    {
        return [
            [['signature','name'], 'string'],
            ['newpwd', 'string', 'min'=>6],
            ['sex', 'in', 'range' =>[1,2]],
            ['pic', 'file', 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxSize'=>2048*1024],
            ['mobile', 'validateMobile'],
            [['mobile', 'newpwd'], 'requiredCode'],
            ['code', 'validateCode'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'signature' => '签名',
            'sex'       => '性别',
            'name'      => '姓名',
            'mobile'    => '手机号码',
            'code'      => '验证码',
            'newpwd'    => '新密码',
            'pic'       => '头像',
        ];
    }

    public function validateMobile($attribute)
    {
        if(!$this->hasErrors()){
            $data = \common\models\Employee::find()->where(['mobile'=>$this->mobile])->andWhere(['<>', 'id', $this->_employee->id])->one();
            if(!empty($data)) $this->addError($attribute, '手机号码已被占用');
        }
    }

    public function requiredCode($attribute)
    {
        if(!$this->hasErrors()){
            if(!isset($this->code)) $this->addError($attribute, '验证码不能为空');
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

    public function update()
    {
        if ($this->validate()) {
            if(isset($this->pic->extension)){
                $imgName = uniqid(md5(microtime(true))) . '.' . $this->pic->extension;
                $err = Func::uploadFile($this->pic->tempName, $imgName);
                if(!empty($err)){
                    $this->addErrors(['pic'=>'上传失败']);
                    return FALSE;
                }
                $this->_employee->pic = Func::getImgUrl($imgName);
            }

            foreach (['signature', 'sex', 'name', 'mobile'] as $field){
                if(isset($this->$field)) $this->_employee->$field = $this->$field;
            }
            $ret = $this->_employee->save();
            if(!$ret){
                $this->addErrors($this->_employee->errors);
                return FALSE;
            }

            if(isset($this->newpwd)){
                $this->_user->setPassword($this->newpwd);
                $ret = $this->_user->save();
                if(!$ret){
                    $this->addErrors($this->_user->errors);
                    return FALSE;
                }
            }

            return TRUE;
        } else {
            return FALSE;
        }
    }

}