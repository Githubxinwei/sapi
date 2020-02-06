<?php
/**
 * Created by PhpStorm.
 * User: lihuien
 * Date: 2018/2/5
 * Time: 17:15
 */

namespace service\controllers;


use service\models\Schedule;
use service\models\ScheduleForm;
use service\base\BaseController;

class ScheduleController extends BaseController
{
    /**
     * @api {post} /coach/schedule/set-schedule?accesstoken=666  设置排班班次
     * @apiVersion  1.0.0
     * @apiName        设置排班班次
     * @apiGroup       schedule
     * @apiPermission 管理员
     *
     * @apiParamExample {json} 请求参数
     *   POST /coach/schedule/set-schedule?accesstoken=666
     *   {
     *    name      //班次名称
     *    start     //开始时间
     *    end       //结束时间
     *    describe //描述
     *    scenario  //场景 新增add | 修改edit
     *   }
     * @apiDescription   设置排班班次
     * <br/>
     * <span><strong>作    者：</strong></span>李慧恩<br/>
     * <span><strong>邮    箱：</strong></span>lihuien@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/2/6
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/schedule/set-schedule?accesstoken=666
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
     *      "message": "设置失败"       //失败原因
     *  }
     */
    public function actionSetSchedule()
    {
        $post  = $this->post;
        $post['companyId'] = $this->companyId;
        $post['venueId']   = $this->venueId;
        $model = new ScheduleForm();
        $model->setScenario($post['scenario']);
        if($model->load($post,'') && $model->validate()){
            if($post['scenario'] == 'add' && $model->insertSave() === true){
             return   $this->success('', '设置成功');
            }
            if($post['scenario'] == 'edit' && $model->updateSave() === true){
                return   $this->success('', '设置成功');
            }
        }
        return $this->error('设置失败');
    }
    /**
     * @api {get} /coach/schedule/index?accesstoken=666  获取排班列表
     * @apiVersion  1.0.0
     * @apiName       获取排班列表
     * @apiGroup       schedule.list
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/schedule/index?accesstoken=666
     * @apiDescription   获取排班列表
     * <br/>
     * <span><strong>作    者：</strong></span>李慧恩<br/>
     * <span><strong>邮    箱：</strong></span>lihuien@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/2/6
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/schedule/index?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
    "code": 1,
    "status": "success",
    "message": "设置成功",
    "data": {
        { 'name':'A',
          'start':'08:00',
          'end'  :'18:00'
           'describe':''描述
        }
     }
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
        $model = Schedule::findAll(['company_id'=>$this->companyId]);
        if(!empty($model)){
            return $this->success($model,'获取成功');
        }
        return $this->error('暂无排班类型');
    }
    /**
     * @api {get} /coach/schedule/view?accesstoken=666&id=1  获取班次详情
     * @apiVersion  1.0.0
     * @apiName        .获取班次详情
     * @apiGroup       schedule.detail
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/schedule/view?accesstoken=666&id=1
     * {
           id ： 1
     * }
     * @apiDescription   获取班次详情
     * <br/>
     * <span><strong>作    者：</strong></span>李慧恩<br/>
     * <span><strong>邮    箱：</strong></span>lihuien@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/2/6
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/schedule/view?accesstoken=666&id=1
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
    "code": 1,
    "status": "success",
    "message": "设置成功",
    "data": {
            'name':'A',
            'start':'08:00',
            'end'  :'18:00'
            'describe':''描述
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "设置失败"    //失败原因
     *  }
     */
    public function actionView($id)
    {
        $model = Schedule::findOne(['id'=>$id]);
        if(!empty($model)){
            return $this->success($model,'获取成功');
        }
        return $this->error('暂无排班类型');
    }
}