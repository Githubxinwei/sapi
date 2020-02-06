<?php
namespace common\models\relations;

use common\models\base\ConsumptionHistory;
use common\models\base\EntryRecord;
use common\models\base\Member;
use common\models\base\MemberBuyClass;
use common\models\base\Employee;
use common\models\base\MissAboutSet;
use common\models\base\Order;
use common\models\base\VenueLimitTimes;
use common\models\LeaveRecord;
use common\models\Organization;
use common\models\BindPack;

trait MemberCardRelations
{
    /**
     * 后台会员管理 - 会员信息查询 - 关联会员表
     * @author Huang hua <huanghua@itsports.club>
     * @create 2017/4/18
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(\common\models\Member::className(), ['id'=>'member_id']);
    }
    /**
     * 后台会员管理 - 会员信息查询 - 关联卡种表
     * @author Huang hua <huanghua@itsports.club>
     * @create 2017/4/18
     * @return \yii\db\ActiveQuery
     */
    public function getCardCategory()
    {
        return $this->hasOne(\common\models\CardCategory::className(), ['id'=>'card_category_id']);
    }
    /**
     * 后台会员管理 - 会员信息查询 - 进场记录表
     * @author Huang hua <huanghua@itsports.club>
     * @create 2017/4/18
     * @return \yii\db\ActiveQuery
     */
    public function getEntryRecord()
    {
        return $this->hasOne(EntryRecord::className(), ['member_card_id'=>'id']);
    }
    /**
     * 后台会员管理 - 会员信息查询 - 会员买课表
     * @author Huang hua <huanghua@itsports.club>
     * @create 2017/4/18
     * @return \yii\db\ActiveQuery
     */
    public function getMemberBuyClass()
    {
        return $this->hasOne(MemberBuyClass::className(), ['member_card_id'=>'id']);
    }

    /**
     * 后台会员管理 - 会员信息查询 - 消费记录表
     * @author 朱梦珂 <zhumengke@itsports.club>
     * @create 2017/5/26
     * @return \yii\db\ActiveQuery
     */
    public function getConsumptionHistory()
    {
        return $this->hasOne(ConsumptionHistory::className(), ['consumption_type_id'=>'id']);
    }

    /**
     * 后台会员管理 - 会员信息查询 - 员工表
     * @author 朱梦珂 <zhumengke@itsports.club>
     * @create 2017/6/8
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::className(), ['id'=>'employee_id']);
    }

    /**
     * 会员课程订单表
     * @author 朱梦珂 <zhumengke@itsports.club>
     * @create 2017/7/21
     * @return \yii\db\ActiveQuery
     */
    public function getMemberCourseOrder()
    {
        return $this->hasOne(\common\models\MemberCourseOrder::className(), ['member_card_id'=>'id']);
    }
    /**
     * 会员卡表 关联 会员请假表
     * @author 侯凯新 <houkaixin@itsports.club>
     * @create 2017/8/3
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveRecord(){
        return $this->hasMany(LeaveRecord::className(), ['leave_employee_id'=>'member_id']);
    }
    /**
     * 会员卡表 关联会员 请假表 （ios）
     * @author 侯凯新 <houkaixin@itsports.club>
     * @create 2017/8/14
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveRecordIos(){
        return $this->hasMany(LeaveRecord::className(),['member_card_id'=>'id'])->onCondition(["and",["<=","leave_start_time",time()],[">=","leave_end_time",time()],["leaveRecord.status"=>1]]);
    }
    /**
     * 会员卡表 关联 组织架构表（pc）
     * @author 侯凯新 <houkaixin@itsports.club>
     * @create 2017/8/14
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization(){
        return $this->hasOne(Organization::className(),['id'=>'venue_id']);
    }
    /**
     * 会员卡表 关联 订单表
     * @author zhumengke <zhumengke@itsports.club>
     * @create 2017/9/4
     * @return \yii\db\ActiveQuery
     */
    public function getOrder(){
        return $this->hasOne(Order::className(), ['card_category_id'=>'id']);
    }
    /**
     * 会员卡冻结设置
     * @author  houkaixn<houkaixn@itsports.club>
     * @create 2017/9/19
     * @return \yii\db\ActiveQuery
     */
    public function getMissAboutSet(){
        return $this->hasOne(MissAboutSet::className(),['venue_id'=>'venue_id']);
    }
    /**
     * 会员场馆次数限制
     * @author  lihuien<lihuien@itsports.club>
     * @create 2017/9/19
     * @return \yii\db\ActiveQuery
     */
    public function getVenueLimitTimesArr(){
        return $this->hasMany(VenueLimitTimes::className(),['member_card_id'=>'id']);
    }

    public function getBindPack()
    {
        return $this->hasOne(BindPack::className(), ['card_category_id' => 'card_category_id']);
    }
}