<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/13
 * Time: 16:15
 */

namespace coach\models;

use common\models\base\MessageCode;
use Yii;
use yii\base\Model;
use common\models\Admin;
use common\models\Employee;

class ResetPasswordForm extends Model
{
    public $mobile;
    public $code;
    public $newCode;
    public $password;
    public $rePassword;
    public $password_reset_token;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    public function validateCodeTime($post)
    {
        //验证码有效时长：5分钟
        $code  = MessageCode::find()->where(['mobile'=>$post['mobile']])->orderBy('id DESC')->one();
        if($code){
            $result = time() - $code->create_at;
            if($result > 300){
                return false;
            }else{
                return true;
            }
        }
    }

    /**
     * @私教端 - 我的 -重置密码 - 验证码验证
     * @author Jiao Bingyang <jiaobingyang@itsports.club>
     * @create 2017/12/14
     * @inheritdoc
     */
    public function validateCode($post)
    {
        $code  = MessageCode::find()->where(['mobile'=>$post['mobile']])->orderBy('id DESC')->one();
        if($code){
            if($post['code'] !== $code->code){
                return false;
            }else{
                return true;
            }
        }
    }

    /**
     * @私教端 - 我的 -重置密码 - 旧密码验证
     * @author Jiao Bingyang <jiaobingyang@itsports.club>
     * @create 2017/12/13
     * @inheritdoc
     */
    public function setPassword($attribute)
    {
        $employee = Employee::findOne(['mobile' => $this->mobile]);
        if($employee){
            $admin = Admin::findOne(['id' => $employee->admin_user_id]);
            if($admin){
                if(!\Yii::$app->security->validatePassword($this->password,$admin->password_hash)){
                    $this->addError($attribute, '旧密码错误');
                }elseif(\Yii::$app->security->validatePassword($this->rePassword,$admin->password_hash)){
                    $this->addError($attribute, '旧密码与新密码一致');
                }
            }
        }
    }
    
    /**
     * @私教端 - 我的 -重置密码
     * @author Jiao Bingyang <jiaobingyang@itsports.club>
     * @create 2017/12/13
     * @inheritdoc
     */
    public function loadPassword($post,$adminId)
    {
        $admin = Admin::findOne(['id' => $adminId]);
        if($admin){
            $admin->setPassword($post['rePassword']);
            $admin->generatePasswordResetToken();
            if($admin->save()) {
                return true;
            }
        }else{
            return false;
        }
    }

}