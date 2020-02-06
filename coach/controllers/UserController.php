<?php
namespace coach\controllers;

use coach\base\AuthBaseController;
use coach\models\ResetPasswordForm;
use common\models\base\Advice;
use common\models\Func;
use common\models\base\Admin;
use common\models\base\Employee;
use common\models\base\Organization;
use coach\models\Course;
use coach\models\CoachInfoForm;
use Yii;

class UserController extends AuthBaseController
{

    public function actionIndex()
    {
        return $this->success($this->user);
    }

    /**
     * @api {get} /coach/user/get-my-profile?accesstoken=123        私教端我的接口
     * @apiVersion  1.0.0
     * @apiName        私教端我的接口
     * @apiGroup       user
     * @apiPermission 管理员
     * @apiParam  {string}            accesstoken        获准许可
     * @apiParamExample {json} 请求参数
     *   GET /coach/user/get-my-profile?accesstoken=123
     * @apiDescription   私教端我的接口
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://qa.aixingfu.net/coach/user/get-my-profile?accesstoken=123
     * @apiSuccess (返回值) {string} data
     * @apiSuccessExample {json}返回值详情（成功）
     *  {
     *      "code": 1,
     *      "status": "success",
     *      "message": "",
     *      "data": {
     *          "name": "唐成",
     *          "password": "$2y$13$67W3A.kyzKQNpqhE8vl0q.9rY3FBeCq4Jshi9.HWt/zX3WStTc0kK",
     *          "pic": "http://oo0oj2qmr.bkt.clouddn.com/fe54addc64bc996b03f0533828b156d3.jpg?e=1513244095&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:dC_n5LTESJ2T9iodIqENDYe3TDM=",
     *          "signature": "依依东望",
     *          "nickname": "司马懿",
     *          "venue": "艾搏尊爵汇馆"
     *      }
     *  }
     * @apiSuccessExample {json}返回值详情（失败）
     * ｛
     *   "code": 0,             //失败标识
     *   "status": "error",     //失败标识
     *   "message": "数据错误", //失败信息
     *   "data": ""             //空值
     * ｝
     */
    public function actionGetMyProfile()
    {
        $admin = Admin::findOne(['id'=>$this->userId]);
        if($admin){
            $employee = Employee::findOne(['id'=>$this->coachId]);
            $venue    = Organization::findOne(['id'=>$employee->venue_id]);
            if($employee){
                $data = [
                    'name'     => $employee->name,
                    'mobile'   => $employee->mobile,
                    'password' => $admin->password_hash,
                    'pic'      => $employee->pic == '' ? '/plugins/user/images/pt.png' : $employee->pic,
                    'signature'=> $employee->signature,
                    'nickname' => $employee->alias == '' ? $employee->name : $employee->alias,
                    'venue'    => $venue->name
                ];
                return $this->success($data);
            }
        }else{
            return $this->error('数据错误');
        }
    }

    /**
     * @api {post} /coach/user/upload-pic?accesstoken=123     上传头像
     * @apiVersion     1.0.0
     * @apiName        上传头像
     * @apiGroup       user
     * @apiPermission 管理员
     * @apiParam  {string}            accesstoken        获准许可
     * @apiParam  {string}            file               头像
     * @apiParamExample {json} 请求参数(accesstoken)
     *   POST /coach/user/upload-pic?accesstoken=123
     *   {
     *        "file":"f27f12d5baccd71fea60daa8420618f6.jpg"，    //头像
     *   }
     * @apiDescription   上传头像
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://qa.aixingfu.net/coach/user/upload-pic?accesstoken=123
     * @apiSuccess (返回值) {string} data
     * @apiSuccessExample {json}返回值详情（成功）
     *   {
     *        "code":1，                      //成功返回标识
     *        "status":"success"，            //成功返回标识
     *        "message":""，
     *        "data":"http://oo0oj2qmr.bkt.clouddn.com/50d639171566a619a2119f33968a232c.jpg?e=1513148802&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:4fTTQVy1lNcyv_kry_Ccpmd7hM0="，       //头像地址
     *   }
     * @apiSuccessExample {json}返回值详情（失败）
     * ｛
     *   "code": 0,             //失败标识
     *   "status": "error",     //失败标识
     *   "message": "上传失败", //失败信息
     *   "data": ""             //空值
     * ｝
     */
    public function actionUploadPic()
    {
        $file       = \coach\models\Func::uploadImage();
        if(is_array($file)){
            return $file;
        }elseif(is_string($file)){
            $data['file'] = $file;
            $model        = new CoachInfoForm();
            if ($model->load($data, '')) {
                $coach = $model->updatePic($this->coachId);
                if ($coach === true) {
                    return $this->success($data['file']);
                }
                $return = Func::setReturnMessageArr($coach,'上传失败');
                return ['code' => 0, 'status' => 'error', 'message' => $return, 'data' => $coach];
            }
            $return = Func::setReturnMessageArr(['验证失败'],'上传失败');
            $result = ['code' => 0, 'status' => 'error', 'message' => $return, 'data' => ['验证失败']];
            return $result;
        }
    }

    /**
     * @api {POST} /coach/user/update-profile?accesstoken=123   更新昵称、个人签名
     * @apiVersion  1.0.0
     * @apiName        更新昵称、个人签名
     * @apiGroup       user
     * @apiPermission 管理员
     * @apiParam  {string}       accesstoken        获准许可
     * @apiParam  {string}       nickname           昵称
     * @apiParam  {string}       signature          签名
     * @apiParamExample {json} 请求参数
     *   POST /coach/user/update-profile?accesstoken=123
     *   {
     *        "nickname" :"糖糖"，             //昵称
     *        "signature":"平安",             //签名
     *   }
     * @apiDescription   更新昵称、个人签名
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://qa.aixingfu.net/coach/user/update-profile?accesstoken=123
     * @apiSuccess (返回值) {string} data
     * @apiSuccessExample {json}返回值详情（成功）
     * {
     *  "code": 1,               //成功返回标识
     *  "status": "success",     //成功返回标识
     *  "message": "",
     *  "data": "成功  "         //成功返回信息
     * }
     * @apiSuccessExample {json}返回值详情（失败）
     * ｛
     *   "code": 0,             //失败标识
     *   "status": "error",     //失败标识
     *   "message": "",
     *   "data": "数据错误"     //失败信息
     * ｝
     */
    public function actionUpdateProfile()
    {
        $data['nickname']   = \Yii::$app->request->post('nickname','hpc');
        $data['signature']  = \Yii::$app->request->post('signature','hpc');
        $model   = new CoachInfoForm();
        $result  = $model->updateProfile($data,$this->coachId);
        if($result){
            return $this->success('成功');
        }else{
            return $this->error('失败');
        }
    }

    /**
     * @api {get} /coach/user/reset-password?accesstoken=123     重置密码
     * @apiVersion  1.0.0
     * @apiName        重置密码
     * @apiGroup       user
     * @apiPermission 管理员
     * @apiParam  {string}     accesstoken     获准许可
     * @apiParam  {string}     mobile          手机号码
     * @apiParam  {string}     code            验证码
     * @apiParam  {string}     password        旧密码
     * @apiParam  {string}     rePassword      新密码
     * @apiParamExample {json} 请求参数
     *   POST /coach/user/reset-password?accesstoken=123
     *   {
     *        "mobile":"15078796678"，         //手机
     *        "code":"123456"，                //验证码 
     *        "password":"******",             //旧密码
     *        "rePressword":"******"，         //新密码
     *   }
     * @apiDescription   重置密码
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://qa.aixingfu.net/coach/user/reset-password?accesstoken=123
     * @apiSuccess (返回值) {string} data
     * @apiSuccessExample {json}返回值详情（成功）
     * {
     *  "code": 1,               //成功返回标识
     *  "status": "success",     //成功返回标识
     *  "message": "",
     *  "data": "重置密码成功"   //成功返回标识
     * }
     * @apiSuccessExample {json}返回值详情（失败）
     * ｛
     *   "code": 0,              //失败标识
     *   "status": "error",      //失败标识
     *   "message": "旧密码错误" //错误描述
     * ｝
     */
    public function actionResetPassword()
    {
        $post  = \Yii::$app->request->post();
        $model = new ResetPasswordForm();
        if($post['code'] == ''){
            return $this->error('请输入验证码');
        }else{
            if($model->validateCodeTime($post)){
                if ($model->validateCode($post)){
                    if($post['password'] == ''){
                        return $this->error('请输入旧密码');
                    }else{
                        $admin = Admin::findOne(['id' => $this->userId]);
                        if($admin){
                            $result = \Yii::$app->security->validatePassword($post['password'],$admin->password_hash);
                            if($result){
                                $data = $model->loadPassword($post,$this->userId);
                                if ($data === true) {
                                    return $this->success('重置密码成功');
                                } else {
                                    return $this->error('重置密码失败');
                                }
                            }else{
                                return $this->error('旧密码错误');
                            }
                        }else{
                            return $this->error('数据错误');
                        }
                    }
                }else{
                    return $this->error('验证码错误');
                }
            }else{
                return $this->error('验证码已失效');
            }
        }
    }

    /**
     * @api {post} /coach/user/send-advice?accesstoken=123        提交建议
     * @apiVersion  1.0.0
     * @apiName        提交建议
     * @apiGroup       user
     * @apiPermission 管理员
     * @apiParam  {string}     accesstoken     获准许可
     * @apiParam  {string}     content         建议内容
     * @apiParamExample {json} 请求参数
     *   POST /coach/user/send-advice?accesstoken=123
     *   {
     *        "content":"对酒当歌"，         //建议内容
     *   }
     * @apiDescription   生成验证码
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://qa.aixingfu.net/coach/user/send-advice?accesstoken=123
     * @apiSuccess (返回值) {string} data
     * @apiSuccessExample {json}返回值详情（成功）
     * {
     *  "code": 1,               //成功返回标识
     *  "status": "success",     //成功返回标识
     *  "message": "",
     *  "data": "提交成功"       //成功返回标识
     * }
     * @apiSuccessExample {json}返回值详情（失败）
     * {
     *  "code": 0,               //失败返回标识
     *  "status": "error",       //失败返回标识
     *  "message": "",
     *  "data": "提交失败"       //失败返回标识
     * }
     */
    public function actionSendAdvice()
    {
        $data    = \Yii::$app->request->post();
        $model   = new CoachInfoForm();
        $result  = $model->sendAdvice($data,$this->userId);
        if($result == true){
            return $this->success('提交成功');
        }else{
            return $this->error('提交失败');
        }
    }

    /**
     * @api {get} /coach/user/get-advice?accesstoken=123      获取提交的建议
     * @apiVersion  1.0.0
     * @apiName        获取提交的建议
     * @apiGroup       user
     * @apiPermission 管理员
     * @apiParam  {string}     accesstoken     获准许可
     * @apiParamExample {json} 请求参数
     *   GET /coach/user/get-advice?accesstoken=123
     * @apiDescription   获取提交的建议
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://qa.aixingfu.net/coach/user/get-advice?accesstoken=123
     * @apiSuccess (返回值) {string} data
     * @apiSuccessExample {json}返回值详情（成功）
     * {
     *  "code": 1,               //成功返回标识
     *  "status": "success",     //成功返回标识
     *  "message": "",
     *  "data": "虎啸龙吟"       //建议内容
     * }
     * @apiSuccessExample {json}返回值详情（失败）
     * {
     *  "code": 0,               //失败返回标识
     *  "status": "error",       //失败返回标识
     *  "message": "",
     *  "data": "暂无数据"       //失败返回标识
     * }
     */
    public function actionGetAdvice()
    {
        $advice = Advice::find()->where(['admin_id'=>$this->userId])->orderBy('id DESC')->one();
        if($advice){
            return $this->success($advice->content);
        }else{
            return $this->error('暂无数据');
        }
    }

    /**
     * @api {get} /coach/user/take-num?type=m&accesstoken=123_1514736000   教练上课量统计
     * @apiVersion  1.0.0
     * @apiName        .教练上课量统计
     * @apiGroup       user
     * @apiPermission 管理员
     * @apiParam  {string}    accesstoken   获准许可
     * @apiParam  {string}    type          日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParamExample {json} 请求参数
     *   GET /coach/user/take-num?type=m&accesstoken=123_1514736000
     * @apiDescription   教练上课量统计
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://qa.aixingfu.net/coach/user/take-num?type=m&accesstoken=123_1514736000
     * @apiSuccess (返回值) {string} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *    "code": 1,                                       //成功返回标识
     *    "status": "success",                             //成功返回标识
     *    "message": "",
     *    "data": [
     *    "sum": 0 //总节数
     *        {
     *            "product_name": "0元WD游泳课程2A",       //课程名字
     *            "num": "20"                              //上课节数
     *        },
     *        {
     *            "product_name": "PT游泳课",             //课程名字
     *            "num": "10"                             //上课节数
     *        }
     *    ]
     *}
     * @apiSuccessExample {json}返回值详情（失败）
     * {
     *  "code": 0,               //失败返回标识
     *  "status": "error",       //失败返回标识
     *  "message": "暂无数据",   //失败返回标识
     * }
     */
    public function actionTakeNum($type)
    {
        $model = new Course();
        $data  = $model->getTakeNum($this->coachId,$type);
        $sum = 0;
        foreach ($data as $v){
            $sum+=$v['num'];
        }
        $datas['sum']=$sum;
        $datas['data']=$data;
        return $this->success($datas);
    }

    /**
     * @api {get} /coach/user/token-course-list?start=1483200000&end=1514735999&accesstoken=123_1514736000   教练上课量报表
     * @apiVersion  1.0.0
     * @apiName        .教练上课量报表.教练上课量报表
     * @apiGroup       user
     * @apiPermission 管理员
     * @apiParam  {string}    accesstoken   获准许可
     * @apiParam  {string}    start         筛选开始时间(unix时间戳)
     * @apiParam  {string}    end           筛选截止时间(unix时间戳)
     * @apiParam  {string}    type          日期类别：d 日、w 周 、m 月 、s 季度 、y 年(type和start/end只能传一种)
     * @apiParamExample {json} 请求参数
     *   GET /coach/user/token-course-list?start=1483200000&end=1514735999&accesstoken=123_1514736000
     * @apiDescription   教练上课量报表
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/15
     * @apiSampleRequest  http://qa.aixingfu.net/coach/user/token-course-list?start=1483200000&end=1514735999&accesstoken=123_1514736000
     * @apiSuccess (返回值) {string} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *    "code": 1,                                       //成功返回标识
     *    "status": "success",                             //成功返回标识
     *    "message": "",
     *    "data": [
     * sum //总节数
     *      {
     *       "username": "张双利",
     *       "mobile": "13526800271",
     *       "token_num": "8",
     *       "total_money": "3000.00"
     *      },
     *    ]
     *}
     * @apiSuccessExample {json}返回值详情（失败）
     * {
     *  "code": 0,               //失败返回标识
     *  "status": "error",       //失败返回标识
     *  "message": "暂无数据",   //失败返回标识
     * }
     */
    public function actionTokenCourseList()
    {
        $start = Yii::$app->request->get('start', 0);
        $end   = Yii::$app->request->get('end', 0);
        $type  = Yii::$app->request->get('type', 0);
        if(in_array($type, ['d', 'w', 'm', 's', 'y'], TRUE)){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
        }
        $model = new Course();
        $data = $model->getTakeList($this->coachId,$start,$end);
        return $data;
        if($data){
            return $this->success($data);
        }else{
            return $this->error('暂无数据');
        }
    }

    /**
     * @api {get} /coach/user/sell-list?type=d&accesstoken=123_1514736000   卖课排行榜
     * @apiVersion  1.0.0
     * @apiName        卖课排行榜
     * @apiGroup       user
     * @apiPermission 管理员
     * @apiParam  {string}    accesstoken   获准许可
     * @apiParam  {string}    type          日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParamExample {json} 请求参数
     *   GET /coach/user/sell-list?type=d&accesstoken=123_1514736000
     * @apiDescription   教练上课量报表
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/15
     * @apiSampleRequest  http://qa.aixingfu.net/coach/user/sell-list?type=d&accesstoken=123_1514736000
     * @apiSuccess (返回值) {string} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *    "code": 1,                    //成功返回标识
     *    "status": "success",          //成功返回标识
     *    "message": "",
     *    "data": [
     *      {
     *       "name": "张双利",          //私教
     *       "member_number": "1",      //会员量
     *       "course_number": "25",     //卖课节数
     *       "total_money": "3000.00"   //成交金额
     *      },
     *    ]
     *}
     * @apiSuccessExample {json}返回值详情（失败）
     * {
     *  "code": 0,               //失败返回标识
     *  "status": "error",       //失败返回标识
     *  "message": "暂无数据",   //失败返回标识
     * }
     */
    public function actionSellList($type)
    {
        $model = new Course();
        $data  = $model->getSellList($this->venueId,$type);
        if($data){
            return $this->success($data);
        }else{
            return $this->error('暂无数据');
        }
    }

    /**
     * @api {get} /coach/user/coach-achievement?start=1483200000&end=1514735999&accesstoken=123_1514736000   教练业绩报表
     * @apiVersion  1.0.0
     * @apiName        教练业绩报表
     * @apiGroup       user
     * @apiPermission 管理员
     * @apiParam  {string}    accesstoken   获准许可
     * @apiParam  {string}    start         筛选开始时间(unix时间戳)
     * @apiParam  {string}    end           筛选截止时间(unix时间戳)
     * @apiParam  {string}    type          日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParamExample {json} 请求参数
     *   GET /coach/user/coach-achievement?start=1483200000&end=1514735999&accesstoken=123_1514736000
     * @apiDescription   教练业绩报表
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/15
     * @apiSampleRequest  http://qa.aixingfu.net/coach/user/coach-achievement?start=1483200000&end=1514735999&accesstoken=123_1514736000
     * @apiSuccess (返回值) {string} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *    "code": 1,                    //成功返回标识
     *    "status": "success",          //成功返回标识
     *    "message": "",
     *    "sum": "39,920.00",//总金额 元
     *    "data": [
     *      {
     *       "username": "范留芙",        //会员姓名
     *       "product_name": "PT游泳课",  //课程名称
     *       "course_amount": "25",       //卖课节数
     *        "unit_price": "400",        //课时费
     *       "money_amount": "10000.00"   //成交金额
     *      },
     *    ]
     *}
     * @apiSuccessExample {json}返回值详情（失败）
     * {
     *  "code": 0,               //失败返回标识
     *  "status": "error",       //失败返回标识
     *  "message": "暂无数据",   //失败返回标识
     * }
     */
    public function actionCoachAchievement()
    {
        $start = Yii::$app->request->get('start', 0);
        $end   = Yii::$app->request->get('end', 0);
        $type  = Yii::$app->request->get('type', 0);
        if(in_array($type, ['d', 'w', 'm', 's', 'y'], TRUE)){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
        }
        $model = new Course();
        $data  = $model->coachAchievement($this->coachId,$start,$end);
        if($data){
            return $this->success($data);
        }else{
            return $this->error('暂无数据');
        }
    }


    
}
