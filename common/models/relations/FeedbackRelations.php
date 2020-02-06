<?php
namespace common\models\relations;
use common\models\base\Member;
use common\models\FeedbackType;
use common\models\MemberDetails;
use service\models\Organization;


trait FeedbackRelations
{
    public function getFeedbackType()
    {
        return $this->hasOne(FeedbackType::className(), ['id' => 'type_id']);
    }
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'user_id']);
    }

    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'venue_id']);
    }
    
}
