<?php

namespace common\models\relations;

use common\models\FollowWay;
trait FollowMemberRelations
{
    /**
     * 后台会员管理 - 会员信息查询 - 关联会员卡表
     * @author Huang Pengju <huangpengju@itsports.club>
     * @create 2017/3/29
     * @return \yii\db\ActiveQuery
     */
    public function getFollowWay()
    {
        return $this->hasOne(FollowWay::className(), ['id'=>'follow_way_id']);
    }

}