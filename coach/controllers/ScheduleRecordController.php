<?php
/**
 * Created by PhpStorm.
 * User: lihuien
 * Date: 2018/2/6
 * Time: 11:19
 */

namespace coach\controllers;


use coach\base\AuthBaseController;
use coach\models\ScheduleRecord;
use coach\models\ScheduleRecordForm;

class ScheduleRecordController extends AuthBaseController
{
    /**
     * @api {post} /coach/schedule-record/set-schedule-record?accesstoken=666  设置员工班次记录
     * @apiVersion  1.0.0
     * @apiName       设置员工班次记录
     * @apiGroup       scheduleRecord
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   POST /coach/schedule-record/set-schedule-record?accesstoken=666
     *   {
     *     coachId;  //教练ID
     *     date;     //日期
     *     scheduleId; //班次ID
     *   }
     * @apiDescription   设置员工班次记录
     * <br/>
     * <span><strong>作    者：</strong></span>李慧恩<br/>
     * <span><strong>邮    箱：</strong></span>lihuien@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/2/6
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/schedule-record/set-schedule-record?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
    "code": 1,
    "status": "success",
    "message": "设置成功",
    "data": ""
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "设置失败"    //失败原因
     *  }
     */
    public function actionSetScheduleRecord()
    {
        $post  = $this->post;
        $post['companyId'] = $this->companyId;
        $post['venueId']   = $this->venueId;
        $post['createId']  = $this->coachId;
        $model = new ScheduleRecordForm();
        if($model->load($post,'') && $model->validate()){
            if($model->save() === true){
                return   $this->success('', '设置成功');
            }
        }
        return $this->error('设置失败');
    }
    /**
     * @api {post} /coach/schedule-record/index?accesstoken=666  获取员工班次记录list
     * @apiVersion  1.0.0
     * @apiName       获取员工班次记录list
     * @apiGroup       scheduleRecordList
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   POST /coach/schedule-record/index?accesstoken=666
     *   {
     *     date_start;  //开始日期
     *     date_end;    //结束日期
     *   }
     * @apiDescription   获取员工班次记录
     * <br/>
     * <span><strong>作    者：</strong></span>李慧恩<br/>
     * <span><strong>邮    箱：</strong></span>lihuien@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/2/6
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/schedule-record/index?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
    "code": 1,
    "status": "success",
    "message": "设置成功",
    "data": ""
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "设置失败"    //失败原因
     *  }
     */
    public function actionIndex()
    {
        $model = new ScheduleRecord();
        $model->date_start = $this->get['date_start'];
        $model->date_end   = $this->get['date_end'];
        $model->load($this->get,'');
        $data  = ScheduleRecord::find()
            ->where(['between','schedule_date',$model->date_start,$model->date_end])
            ->groupBy('coach_id')->all();
        if(!empty($data)){
//          var_dump($data);die();
//          var_dump($data[0]->coach);
//          var_dump(json_encode($data));die();
            return $this->success($data,'获取成功');
        }
        return $this->error('获取失败');
    }
}