<?php
namespace common\models\relations;

use common\models\MemberCard;
use common\models\base\CoursePackageDetail;
use common\models\base\Order;

trait  MemberCourseOrderRelations
{
    /**
     * 后台 - 会员私课订单 - 关联收费课程表
     * @return string
     * @auther huangpegnju
     * @create 2017-5-25
     * @param
     */
    public function getChargeClass()
    {
        return $this->hasOne(\common\models\ChargeClass::className(), ['id' => 'product_id']);
    }

    /**
     * 后台 - 会员私课订单 - 关联订单详情表
     * @return string
     * @auther huangpegnju
     * @create 2017-6-1
     * @return mixed
     */
    public function getMemberCourseOrderDetails()
    {
        return $this->hasMany(\common\models\MemberCourseOrderDetails::className(),['course_order_id'=>'id']);
    }

    public function getMemberCourseOrderDetailsOne()
    {
        return $this->hasOne(\common\models\MemberCourseOrderDetails::className(),['course_order_id'=>'id']);
    }

    /**
     * 后台 - 会员私课订单 - 关联员工表
     * @return string
     * @auther 朱梦珂
     * @create 2017-6-2
     * @return mixed
     */
    public function getEmployee()
    {
        return $this->hasOne(\common\models\Employee::className(),['id'=>'seller_id']);
    }
    public function getEmployeeS()
    {
        return $this->hasOne(\common\models\Employee::className(),['id'=>'private_id']);
    }
    /**
     * 私课管理 - 私课排期 - 关联会员表
     * @return string
     * @author 黄华
     * @create 2017-6-2
     * @return mixed
     */
    public function getMember()
    {
        return $this->hasOne(\common\models\Member::className(),['id'=>'member_id']);
    }
    /**
     * 销售主页 - 课程预约 - 关联课种表
     * @return string
     * @author 黄鹏举
     * @create 2017-6-14
     * @return mixed
     */
    public function getCourse()
    {
        return $this->hasOne(\common\models\Course::className(),['id'=>'course_id']);
    }
    /**
     * 销售主页 - 课程预约 - 关联课种表
     * @return string
     * @author 黄鹏举
     * @create 2017-6-14
     * @return mixed
     */
    public function getCoursePackageDetail()
    {
        return $this->hasOne(CoursePackageDetail::className(),['charge_class_id'=>'product_id']);
    }
    /**
     *  订单表关联会员卡表
     * @return string
     * @author 侯凯新
     * @create 2017-8-7
     * @return mixed
     */
    public function getMemberCard(){
        return $this->hasMany(MemberCard::className(),['member_id'=>'member_id'])
                ->onCondition([">","invalid_time",time()])->orderBy(["id"=>SORT_DESC]);
    }
    /**
     *  订单表关联会员卡表
     * @return string
     * @author 朱梦珂
     * @create 2017-8-17
     * @return mixed
     */
    public function getMemberCardS()
    {
        return $this->hasOne(MemberCard::className(),['id'=>'member_card_id']);
    }
    /**
     * 会员私课订单表 关联 订单表
     * @author zhumengke <zhumengke@itsports.club>
     * @create 2017/9/5
     * @return \yii\db\ActiveQuery
     */
    public function getOrder(){
        return $this->hasOne(Order::className(), ['consumption_type_id'=>'id'])->onCondition(['consumption_type'=>['charge', 'chargeGroup']]);
    }

    /**
     * 会员私课订单表 关联 私教
     * @author houkaixin<houkaixin@itsports.club>
     * @create 2017/11/10
     * @return \yii\db\ActiveQuery
     */
    public function getOrderList(){
        return $this->hasOne(\common\models\Order::className(),['consumption_type_id'=>'id'])->onCondition(["consumption_type"=>"charge"]);
    }

    public function getMemberDetails()
    {
        return $this->hasOne(\common\models\MemberDetails::className(),['member_id'=>'member_id']);
    }

}