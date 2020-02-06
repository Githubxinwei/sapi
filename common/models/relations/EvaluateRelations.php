<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/17
 * Time: 上午 09:52
 */

namespace common\models\relations;

// use common\models\Organization;
// use common\models\MemberDetails;
use common\models\GroupClass;
use common\models\Member;
use common\models\Organization;

trait  EvaluateRelations
{
    public function getGroupClass()
    {
        return $this->hasOne(GroupClass::className(),['id'=>'consumption_type_id'])
        	->onCondition(["consumption_type" => 'teamClass'])
        	->orOnCondition(["consumption_type" => 'smallClass']);
    }
    public function getMember()
    {
        return $this->hasOne(Member::className(),['id'=>'member_id']);
    }
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(),['id'=>'venue_id']);
    }
}