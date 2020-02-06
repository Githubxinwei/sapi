<?php
namespace service\controllers;


use common\models\Func;
use service\base\BaseController;
use service\models\CoachInfoForm;
use service\models\Course;
use Yii;
use service\models\Employee;
use service\models\UserUpdateForm;
use common\models\Advice;
use yii\web\UploadedFile;

class UserController extends BaseController
{
    /**
     * @api {get} /service/user/info  我的资料
     * @apiName        1我的资料
     * @apiGroup       user
     * @apiDescription   我的资料
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/17
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/user/info
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "id": "1813",
            "name": "范登科",
            "age": null,
            "sex": 1,
            "mobile": "18739952273",
            "organization_id": "85",
            "position": "wefafea",
            "status": 1,
            "pic": "",
            "work_time": null,
            "company_id": "75",
            "venue_id": "76",
            "signature":"sdfsdf",//签名
            "company_name": "郑州市艾搏健身服务有限公司",
            "venue_name": "艾搏尊爵汇馆",
            "organization_name": "私教部"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "Object not found: 3",
        "code": 0,
        "status": 404
    }
     */
    public function actionInfo()
    {
        return Employee::findOne($this->employeeId);
    }

     /**
     * @api {POST} /service/user/update  修改我的资料
     * @apiName        2修改我的资料
     * @apiGroup       user
      * @apiParam  {string}               signature       签名
      * @apiParam  {string}               sex             性别1男2女
      * @apiParam  {string}               name            姓名
      * @apiParam  {string}               mobile          手机号码
      * @apiParam  {string}               code            验证码
      * @apiParam  {string}               newpwd          新密码
      * @apiParam  {file}                 pic             头像图片
     * @apiDescription   修改我的资料
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/17
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/user/update
      * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "修改成功",
        "code": 1,
        "status": 200,
        "data": {
            "message": "修改成功"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "",
        "code": 0,
        "status": 422,
        "data": [
            {
                "field": "code",
                "message": "验证码错误"
            }
        ]
    }
     */
    public function actionUpdate()
    {
        $model = new UserUpdateForm(['user_id'=>$this->userId, 'employee_id'=>$this->coachId]);
        $model->load($this->post, '');
        return $model->update() ? $this->success('修改成功') : $this->modelError($model->errors);
    }

    /**
     * @api {POST} /service/user/advice  意见反馈
     * @apiName        3意见反馈
     * @apiGroup       user
      * @apiParam  {string}               content       反馈内容
     * @apiDescription   意见反馈
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/22
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/user/advice
      * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "修改成功",
        "code": 1,
        "status": 200,
        "data": {
            "message": "修改成功"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "",
        "code": 0,
        "status": 422,
        "data": [
            {
                "field": "content",
                "message": "反馈内容不能为空"
            }
        ]
    }
     */
    public function actionAdvice()
    {
        $content = Yii::$app->request->post('content', '');
        if(empty($content)) return $this->error('反馈内容不能为空');
        $model = new Advice();
        $model->admin_id = $this->userId;
        $model->content = $content;
        $model->create_at = time();
        return $model->save() ? $this->success('反馈成功') : $this->modelError($model->errors);
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
