<?php
/**
 * Created by PhpStorm.
 * User: lihuien
 * Date: 2017/4/13
 * Time: 20:29
 */

namespace common\models\relations;

use common\models\base\Employee;
use common\models\base\Seat;
use common\models\ChargeClass;
use common\models\MemberCard;
use common\models\Classroom;
use common\models\MemberCourseOrder;
trait AboutClassRelations
{
    /**
     * 后台会员管理 - 约课信息查询 - 关联课程表
     * @author 李慧恩 <lihuien@itsports.club>
     * @create 2017/4/11
     * @return \yii\db\ActiveQuery
     */
    public function getGroupClass()
    {
        return $this->hasOne(\common\models\GroupClass::className(), ['id'=>'class_id']);
    }
    public function getMemberCourseOrder()
    {
        return $this->hasMany(MemberCourseOrder::className(), ['member_id'=>'member_id']);
    }
    /**
     * 后台会员管理 - 约课信息查询 - 关联课程表
     * @author 李慧恩 <lihuien@itsports.club>
     * @create 2017/4/11
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(\common\models\Employee::className(), ['id'=>'coach_id']);
    }
    /**
     * 后台会员管理 - 约课信息查询 - 关联会员表
     * @author 李慧恩 <lihuien@itsports.club>
     * @create 2017/4/11
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(\common\models\Member::className(), ['id'=>'member_id']);
    }
    /**
     * 后台会员管理 - 约课信息查询 - 关联课程表
     * @author 李慧恩 <lihuien@itsports.club>
     * @create 2017/4/11
     * @return \yii\db\ActiveQuery
     */
    public function getChargeClass()
    {
        return $this->hasOne(ChargeClass::className(), ['id'=>'class_id']);
    }
    /**
     * 后台私课管理 - 会员上课信息查询  - 关联会员购买私课订单详情表
     * @author 黄华 <lihuien@itsports.club>
     * @create 2017/6/3
     * @return \yii\db\ActiveQuery
     */
    public function getMemberCourseOrderDetails()
    {
        return $this->hasOne(\common\models\MemberCourseOrderDetails::className(),['id'=>'class_id']);
    }
    /**
     * 后台私课管理 - 会员上课信息查询  - 关联会员购买私课订单详情表
     * @author houkaixin<houkaixin@itsports.club>
     * @create 2017/6/3
     * @return \yii\db\ActiveQuery
     */
    public function getIosMemberCourseOrderDetails()
    {
        return $this->hasMany(\common\models\MemberCourseOrderDetails::className(),['id'=>'class_id']);
    }
    /**
     * 后台管理 - 会员管理（团课）- 关联会员卡表
     * @author 侯凯新 <houkaixin@itsports.club>
     * @create 2017/7/7
     * @return \yii\db\ActiveQuery
     */
    public function getMemberCard(){
        return $this->hasOne(MemberCard::className(),['id'=>'member_card_id']);
    }
    /**
     * 后台管理 - 会员管理（团课）- 关联座位
     * @author 侯凯新 <houkaixin@itsports.club>
     * @create 2017/7/7
     * @return \yii\db\ActiveQuery
     */
    public function getSeat(){
        return $this->hasOne(Seat::className(),['id'=>'seat_id']);
    }

    public function getMemberDetails()
    {
        return $this->hasOne(\common\models\MemberDetails::className(), ['member_id'=>'member_id']);
    }
}