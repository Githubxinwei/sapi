<?php
namespace common\models\relations;

use common\models\base\Member;
use common\models\base\MemberCard;

trait InformationRecordsRelations
{

    /**
     * 后台会员管理 - 行为记录 - 关联会员表
     * @author Huang hua <huanghua@itsports.club>
     * @create 2017/8/5
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id'=>'member_id']);
    }
    /**
     * 后台会员管理 - 行为记录 - 关联会员卡表
     * @author Huang hua <huanghua@itsports.club>
     * @create 2017/8/5
     * @return \yii\db\ActiveQuery
     */
    public function getMemberCard()
    {
        return $this->hasOne(MemberCard::className(), ['id'=>'member_card_id']);
    }

}