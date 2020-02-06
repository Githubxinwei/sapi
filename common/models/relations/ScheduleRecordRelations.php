<?php
/**
 * Created by PhpStorm.
 * User: lihuien
 * Date: 2018/2/6
 * Time: 14:43
 */

namespace common\models\relations;


use common\models\Employee;

trait ScheduleRecordRelations
{
    public function getEmployee()
    {
        return $this->hasOne(Employee::className(),['id'=>'coach_id']);
    }
}