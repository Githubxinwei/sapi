<?php

namespace common\models;

use Yii;
use \common\models\base\MemberCard as MemberCardBase;
use common\models\relations\MemberRelations;

class Member extends \common\models\base\Member
{
    use MemberRelations;

    /**
     * 云运动 - 团课座位 - 返回会员类型
     * @author zhumengke <zhumengke@itsports.club>
     * @create 2018/3/29
     * @param  $aboutClass
     * @param  $venueId
     * @return string
     */
    public static function getMemberType($aboutClass)
    {
        $member = Member::findOne(['id' => $aboutClass['member_id']]);
        if($member['member_type'] === 2){
            return 2;    //潜在会员，红标会员2
        }else{
            $memberCard = MemberCardBase::find()
                ->where(['member_id' => $aboutClass['member_id']])
                ->andWhere(['venue_id' => $member['venue_id']])
                ->orderBy(['create_at' => SORT_ASC])
                ->asArray()
                ->one();
            if(time()-$memberCard['create_at'] > 30*24*60*60){
                return 0;    //一个月以上办卡会员
            }else{
                return 1;    //一个月内新办卡会员，黄标会员1
            }
        }
    }
}