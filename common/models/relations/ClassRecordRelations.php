<?php
namespace common\models\relations;

use common\models\base\Member;
use common\models\base\Employee;

trait  ClassRecordRelations
{
    /**
     * 后台 - 会员管理 - 关联会员表
     * @return string
     * @author 黄华
     * @create 2017-4-22
     * @param
     */
    public function getMember(){
        return $this->hasOne(\common\models\Member::className(), ['id'=>'member_id']);
    }
    /**
     * 后台 - 私课管理 - 关联员工表
     * @return string
     * @author 黄华
     * @create 2017-5-24
     * @param
     */
    public function getEmployee(){
        return $this->hasOne(Employee::className(), ['id'=>'coach_id']);
    }
  
}