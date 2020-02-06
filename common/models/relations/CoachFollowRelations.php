<?php
namespace common\models\relations;

use common\models\base\Employee;
use common\models\base\Member;


trait CoachFollowRelations
{

    public function getEmployee()
    {
        return $this->hasOne(\common\models\Employee::className(), ['id'=>'coach_id']);
    }
    public function getMember()
    {
        return $this->hasOne(\common\models\Member::className(), ['id'=>'member_id']);
    }

}