<?php
namespace service\models;

use common\models\Employee;
use Yii;
use common\models\base\MessageCode;
use yii\base\Model;
use yii\db\Exception;

class RegisterForm extends Model
{
    public $username;
    public $mobile;
    public $code;
    public $truename;
    public $company_id;
    public $venue_id;
    public $partment_id;
    public $password;
    private $_user;

    public function rules()
    {
        return [
            [['username','mobile','code','password','truename','company_id','venue_id','partment_id'], 'required'],
            ['password', 'string', 'min'=>6],
            ['username', 'unique', 'targetClass' => '\common\models\Admin', 'targetAttribute'=>'username'],
            ['mobile', 'unique', 'targetClass' => '\common\models\Employee', 'targetAttribute'=>'mobile'],
            ['code', 'validateCode'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'username' => '用户名',
            'mobile' => '手机号码',
            'code'   => '验证码',
            'password' => '密码',
            'truename' => '真实姓名',
            'company_id' => '公司',
            'venue_id' => '场馆',
            'partment_id' => '部门',
        ];
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

    public function register()
    {
        if ($this->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $model = new User();
                $model->username = $this->username;
                $model->generateAuthKey();
                $model->setPassword($this->password);
                $model->status = User::STATUS_READY;
                $model->created_at = $model->updated_at = time();
                if(!$model->save()) throw new Exception(json_encode($model->errors));

                $employee = new Employee();
                $employee->name = $this->truename;
                $employee->mobile = $this->mobile;
                $employee->company_id = $this->company_id;
                $employee->venue_id = $this->venue_id;
                $employee->organization_id = $this->partment_id;
                $employee->admin_user_id = $model->id;
                $employee->created_at = $employee->updated_at = time();
                $employee->create_id = $model->id;
                if(!$employee->save()) throw new Exception(json_encode($employee->errors));

                $this->_user = $model;
                $transaction->commit();
                return TRUE;
            }catch (Exception $e){
                $transaction->rollBack();
                var_dump($e->getMessage());exit;
                return FALSE;
            }
        }
        return FALSE;
    }


    /**
     * 注册成功后返回客户端的会员信息
     * @return array
     */
    public function info()
    {
        return $this->_user->getApiInfo();
    }
}