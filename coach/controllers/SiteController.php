<?php
namespace coach\controllers;

use coach\base\BaseController;
use coach\models\FindPwdForm;
use coach\models\LoginCodeForm;
use coach\models\LoginForm;

class SiteController extends BaseController
{
    /**
     * @api {post} /coach/site/login   私教登录
     * @apiVersion  1.0.0
     * @apiName        私教登录
     * @apiGroup       site
     * @apiPermission 管理员
     * @apiParam  {string}        mobile       手机号码
     * @apiParam  {string}        password       密码
     * @apiParamExample {json} 请求参数
     *   POST /coach/site/login
     *   {
     *        "mobile":"17796655023"，   //手机号码
     *        "password":"123456"        //密码
     *   }
     * @apiDescription   私教登录
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/8
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/site/login
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     * {
     *  "code":1,               //成功标识
     *  "status": "success",    //请求状态
     *  "message": "请求成功"，  //返回信息
     *  "data": {
     *      "accesstoken": "Z1MaRW6N2zmu2FrnebBHhU_zVTHU_Qov", //accesstoken,登录后的操作每次请求都需要加上?accesstoken=Z1MaRW6N2zmu2FrnebBHhU_zVTHU_Qov
     *      "manage": true                                     //是否私教经理
     *     }
     *   }
     * }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "手机号码或密码错误.;"  //失败原因
     *  }
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load($this->post, '');

        return $model->login() ? $this->success($model->info()) : $this->error($this->toString($model->errors));
    }

    /**
     * @api {post} /coach/site/login-code   验证码登录
     * @apiVersion  1.0.0
     * @apiName        验证码登录
     * @apiGroup       site
     * @apiPermission 管理员
     * @apiParam  {string}        mobile     手机号码
     * @apiParam  {string}        code       验证码
     * @apiParamExample {json} 请求参数
     *   POST /coach/site/login
     *   {
     *        "mobile":"17796655023"，   //手机号码
     *        "code":"123456"            //验证码
     *   }
     * @apiDescription   验证码登录
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/8
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/site/login-code
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     * {
     *  "code":1,               //成功标识
     *  "status": "success",    //请求状态
     *  "message": "请求成功"，  //返回信息
     *  "data": {
     *      "accesstoken": "Z1MaRW6N2zmu2FrnebBHhU_zVTHU_Qov", //accesstoken
     *      "manage": true                                     //是否私教经理
     *     }
     *   }
     * }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "手机号码不存在.;"  //失败原因
     *  }
     */
    public function actionLoginCode()
    {
        $model = new LoginCodeForm();
        $model->load($this->post, '');

        return $model->login() ? $this->success($model->info()) : $this->error($this->toString($model->errors));
    }

    /**
     * @api {post} /coach/site/find-pwd   找回密码
     * @apiVersion  1.0.0
     * @apiName        找回密码找回密码
     * @apiGroup       site
     * @apiPermission 管理员
     * @apiParam  {string}        mobile       手机号码
     * @apiParam  {string}        code         验证码
     * @apiParam  {string}        newpwd       新密码
     * @apiParamExample {json} 请求参数
     *   POST /coach/site/find-pwd
     *   {
     *        "mobile":"17796655023"，   //手机号码
     *        "code":"123456",           //验证码
     *        "newpwd":"123456"          //新密码
     *   }
     * @apiDescription   找回密码找回密码
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/8
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/site/find-pwd
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     * {
     *  "code":1,               //成功标识
     *  "status": "success",    //请求状态
     *  "message": "请求成功"，  //返回信息
     *  "data": {
     *      "accesstoken": "Z1MaRW6N2zmu2FrnebBHhU_zVTHU_Qov", //accesstoken
     *      "manage": true                                     //是否私教经理
     *     }
     *   }
     * }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "手机号码不存在"    //失败原因
     *  }
     */
    public function actionFindPwd()
    {
        $model = new FindPwdForm();
        $model->load($this->post, '');

        return $model->reset() ? $this->success($model->info()) : $this->error($this->toString($model->errors));
    }

}
