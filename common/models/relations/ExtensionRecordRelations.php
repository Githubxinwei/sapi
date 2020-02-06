<?php
namespace common\models\relations;
use common\models\base\MemberCourseOrder;
trait  ExtensionRecordRelations
{
    /**
     * 后台 - 会员管理 - 关联会员课程订单表
     * @return string
     * @author 黄华
     * @create 2017-10-13
     * @param
     */
    public function getMemberCourseOrder(){
        return $this->hasOne(MemberCourseOrder::className(), ['id'=>'course_order_id']);
    }

}