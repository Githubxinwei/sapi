<?php
namespace common\models\relations;

use common\models\Employee;
use common\models\MemberDetails;
use common\models\Member;
use common\models\MemberCard;

trait  CoachAssignMemberRecordRelations
{
    /**
     * 教练分配 - 会员 关联表
     * @DateTime: 2018-04-09
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(),['id'=>'member_id']);
    }

    /**
     * 被分配会员 - 会员详细信息 关联表
     * @DateTime: 2018-04-09
     */
    public function getMemberDetails()
    {
        return $this->hasOne(MemberDetails::className(),['member_id'=>'member_id']);
    }

    public function getMemberCard()
    {
        return $this->hasOne(MemberCard::className(), ['member_id'=>'member_id']);
    }

    /**
     * 被分配到的教练 - 员工 关联表
     * @DateTime: 2018-04-09
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::className(),['id'=>'coach_id']);
    }

    /**
     * 分配人 - 员工 关联表
     * @DateTime: 2018-04-09
     */
    public function getManagement()
    {
        return $this->hasOne(Employee::className(),['id'=>'create_id']);
    }
}