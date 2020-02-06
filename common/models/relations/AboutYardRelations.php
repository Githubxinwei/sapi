<?php
/**
 * Created by PhpStorm.
 * User: lihuien
 * Date: 2017/4/13
 * Time: 20:29
 */

namespace common\models\relations;
use common\models\base\VenueYard;
use common\models\EntryRecord;
use common\models\Member;

trait AboutYardRelations
{
    /**
     * 后台会员管理 - 约课信息查询 - 关联课程表
     * @author 李慧恩 <lihuien@itsports.club>
     * @create 2017/4/11
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id'=>'member_id']);
    }

    public function getEntryRecord(){
        return $this->hasMany(EntryRecord::className(), ['member_id'=>'member_id'])->onCondition(["between","entry_time",constant("classDate"),constant("classStart")]);
    }

    public function getVenueYard(){
        return $this->hasOne(VenueYard::className(),['id'=>'yard_id']);
    }
}
?>