<?php
namespace service\models;

use common\models\base\Admin;
use common\models\base\Advice;
use Yii;
use yii\base\Model;
use common\models\base\Employee;

class CoachInfoForm extends Model
{
    public $file;
    public $mobile;
    public $nickname;
    public $signature;
    public $password;
    public $newCode;

    public function rules()
    {
        return [
            ['file', 'string', 'message' => '图片不能为空'],
        ];
    }

    public function updatePic($employeeId)
    {
        $employee = Employee::findOne(['id' => $employeeId]);
        if ($employee) {
            $employee->pic = $this->file;
            $employee->updated_at = time();
            if ($employee->save()) {
                return true;
            } else {
                return $employee->errors;
            }
        }
    }
    
    public function updateProfile($data,$coachId)
    {
        $employee = Employee::findOne(['id'=>$coachId]);
        if($data['signature'] !== 'hpc'){
            $employee->signature = $data['signature'];
        }
        if($data['nickname'] !== 'hpc'){
            $employee->alias   = $data['nickname'];
        }
        if($employee->save()){
            return true;
        }else{
            return $employee->errors;
        }
    }
    
    public function sendAdvice($data,$adminId)
    {
        $advice = new Advice();
        $advice->admin_id  = $adminId;
        $advice->content   = $data['content'];
        $advice->create_at = time();
        if($advice->save()){
            return true;
        }else{
            return $advice->errors;
        }
    }
    
}