<?php
namespace coach\controllers;

use coach\base\AuthBaseController;
use common\models\AboutClass;
use common\models\Func;
use common\models\LeaveRecord;

class MessageController extends AuthBaseController
{
    /**
     * @api {get} /coach/message/index?accesstoken=666   消息首页
     * @apiVersion  1.0.0
     * @apiName        消息首页
     * @apiGroup       message
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/message/index?accesstoken=666
     * @apiDescription   消息首页
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/13
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/message/index?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "code": 1,
        "status": "success",
        "message": "",
        "data": {
            "about": {
                "count": 0,//未读预约消息数
                "content": "",//预约消息
                "time": ""
            },
            "cancel": {
                "count": 24,//未读取消消息数
                "content": "崔佳慧取消了11月25日 16:20的PT常规课",//取消消息
                "time": "16:20"
            },
            "leave": {
                "count": 12,//未读请假消息数
                "content": "冯敏于11月29日 12:47请假",//请假消息
                "time": "12:47"
            }
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionIndex()
    {
        //预约
        $about = AboutClass::find()->where(['coach_id'=>$this->coachId, 'type'=>1, 'status'=>1])->orderBy('is_read asc, id desc')->one();
        if($about){
            $data['about']['count'] = (int)AboutClass::find()->where(['coach_id'=>$this->coachId, 'type'=>1, 'status'=>1, 'is_read'=>0])->count();
            $member_name = Func::getRelationVal($about, 'memberDetails', 'name');
            $time = date('m月d日 H:i', $about->start);
            $product_name = Func::getRelationVal($about, 'memberCourseOrderDetails', 'product_name');
            $data['about']['content'] = "{$member_name}预约了{$time}的{$product_name}";
            $data['about']['time'] = date('H:i', $about->start);
        }else{
            $data['about']['count'] = 0;
            $data['about']['content'] = '';
            $data['about']['time'] = '';
        }

        //取消
        $cancel = AboutClass::find()->where(['coach_id'=>$this->coachId, 'type'=>1, 'status'=>2])->orderBy('is_read asc, id desc')->one();
        if($cancel){
            $data['cancel']['count'] = (int)AboutClass::find()->where(['coach_id'=>$this->coachId, 'type'=>1, 'status'=>2, 'is_read'=>0])->count();
            $member_name = Func::getRelationVal($cancel, 'memberDetails', 'name');
            $time = date('m月d日 H:i', $cancel->start);
            $product_name = Func::getRelationVal($cancel, 'memberCourseOrderDetails', 'product_name');
            $data['cancel']['content'] = "{$member_name}取消了{$time}的{$product_name}";
            $data['cancel']['time'] = date('H:i', $cancel->start);
        }else{
            $data['cancel']['count'] = 0;
            $data['cancel']['content'] = '';
            $data['cancel']['time'] = '';
        }

        //请假
        $leave = LeaveRecord::find()->joinWith('memberCourseOrder mco', FALSE)->where(['mco.private_id'=>$this->coachId])->orderBy('is_read asc, id desc')->one();
        if($leave){
            $data['leave']['count'] = (int)LeaveRecord::find()->joinWith('memberCourseOrder mco', FALSE)->where(['mco.private_id'=>$this->coachId, 'is_read'=>0])->count();
            $member_name = Func::getRelationVal($leave, 'memberDetails', 'name');
            $time = date('m月d日 H:i', $leave->create_at);
            $data['leave']['content'] = "{$member_name}于{$time}请假";
            $data['leave']['time'] = date('H:i', $leave->create_at);
        }else{
            $data['leave']['count'] = 0;
            $data['leave']['content'] = '';
            $data['leave']['time'] = '';
        }

        return $this->success($data);
    }

    /**
     * @api {get} /coach/message/count?accesstoken=666   未读消息数
     * @apiVersion  1.0.0
     * @apiName        未读消息数
     * @apiGroup       message
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/message/count?accesstoken=666
     * @apiDescription   未读消息数
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/14
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/message/count?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "code": 1,
        "status": "success",
        "message": "",
        "data": {
            "count": 36 //未读消息数
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionCount()
    {
        $aboutnum = AboutClass::find()->where(['coach_id'=>$this->coachId, 'type'=>1, 'status'=>[1,2], 'is_read'=>0])->count();
        $leavenum = LeaveRecord::find()->joinWith('memberCourseOrder mco', FALSE)->where(['mco.private_id'=>$this->coachId, 'is_read'=>0])->count();
        $count = intval($aboutnum+$leavenum);

        return $this->success(['count'=>$count]);
    }


}
