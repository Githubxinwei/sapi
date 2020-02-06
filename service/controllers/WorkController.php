<?php
namespace service\controllers;

use common\models\Clock;
use common\models\EntryRecord;
use common\models\AboutClass;
use service\base\BaseController;
use service\models\Employee;
use service\models\Organization;
use Yii;

class WorkController extends BaseController
{
    /**
     * @api {get} /service/work/index?accesstoken=666   工作首页
     * @apiVersion  1.0.0
     * @apiName        工作首页工作首页
     * @apiGroup       work
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/work/index?accesstoken=666
     *    postion  登录时返回的123
     *    all =1 全部模块
     * @apiDescription   工作首页工作首页
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/work/index?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     * {
    "code": 1,
    "status": "success",
    "message": "",
    -"data": {
    -"common": { //常用
    "001": "卡种审批",
    "002": "退款审批",
    "003": "到店统计",
    "004": "到店卡种统计"
    },
    -"approval": { 审批
    "001": "卡种审批",
    "002": "退款审批"
    },
    -"statistics": { 统计
    "003": "到店统计",
    "004": "到店卡种统计",
    "005": "会员上课统计",
    "006": "销售额统计",
    "007": "员工业绩统计",
    "008": "课程预约统计",
    "009": "销售排行榜",
    "010": "卖课排行榜",
    "011": "上课统计",
    "012": "卡种收入统计"
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
        $postion =3;
        if ($this->user->employee->organization->name == '私教部'){
            if ($this->user->isManager){
                $postion =1;
            }else{
                $postion=2;
            }
        }
        if ($this->user->employee->organization->name == '销售部'){
            $postion =4;
        }
        $all = Yii::$app->request->get('all',0);
        //读取数据库常用模块
        $model = \common\models\Employee::findOne($this->employeeId);
        $array =  Yii::$app->params['general_module'];
        if (empty($model->general_module)){
            $model = Yii::$app->params['position'][$postion];
            $model =  Yii::$app->params[$model]['common'];
            foreach ($model['data'] as $key=>$value){
                $data['common'][$value]=$array[$value];
            }
        }else{
            $comm =$model->general_module;
            $comms = json_decode($comm);
            foreach ($comms as $key=>$value){
                $data['common'][$value]=$array[$value];
            }
        }
        //外面的常用模块

        //点开的全部模块
        if ($all == 1){
            if (empty($postion)) return $this->error('异常!');
            $model = Yii::$app->params['position'][$postion];
            $model =  Yii::$app->params[$model];
            unset($model['common']);
            foreach ($model as $k=>$value){
                if (empty($value['data'])){
                    unset($model[$k]);
                }
            }
            foreach ($model as $key=>$val){
                foreach ($val['data'] as $value){
                    $data[$key][$value]=$array[$value];
                }
            }
        }
        return $this->success($data);
    }

     /**
     * @api {get} /coach/work/in?accesstoken=666   私教上班打卡
     * @apiVersion  1.0.0
     * @apiName        私教上班打卡
     * @apiGroup       work
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/work/in?accesstoken=666
     * @apiDescription   上课打卡
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/work/in?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
     * {
     *  "code":1,               //成功标识
     *  "status": "success",    //请求状态
     *  "message": "打卡成功"，  //返回信息
     *  "data": "",
     * }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "打卡失败"    //失败原因
     *  }
     */
    public function actionIn()
    {
        $today = date('Y-m-d');
        $clock = Clock::findOne(['employee_id'=>$this->coachId, 'date'=>$today]);
        if($clock) return $this->error('已打上班卡');
        $clock = new Clock();
        $clock->employee_id = $this->coachId;
        $clock->date = $today;
        $clock->in_time = time();
        return $clock->save() ? $this->success('','打卡成功') : $this->error('打卡失败', $clock->errors);
    }

     /**
     * @api {get} /coach/work/out?accesstoken=666   私教下班打卡
     * @apiVersion  1.0.0
     * @apiName        2私教下班打卡
     * @apiGroup       work
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/work/out?accesstoken=666
     * @apiDescription   私教下班打卡
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/work/out?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
     * {
     *  "code":1,               //成功标识
     *  "status": "success",    //请求状态
     *  "message": "打卡成功"，  //返回信息
     *  "data": "",
     * }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "打卡失败"    //失败原因
     *  }
     */
    public function actionOut()
    {
        $today = date('Y-m-d');
        $clock = Clock::findOne(['employee_id'=>$this->coachId, 'date'=>$today]);
        if(!$clock) return $this->error('还未打上班卡');
        $clock->out_time = time();
        return $clock->save() ? $this->success('','打卡成功') : $this->error('打卡失败', $clock->errors);
    }

    /**
     * @api {get} /coach/work/clock?accesstoken=666   私教当天打卡信息
     * @apiVersion  1.0.0
     * @apiName        4私教当天打卡信息
     * @apiGroup       work
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/work/clock?accesstoken=666
     * @apiDescription   私教当天打卡信息
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/08
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/work/clock?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "in": "9:00",           //上班时间
            "out": "18:00",         //下班时间
            "now": "1515400947",    //当前时间
            "in_time": "0",         //上班已打卡时间
            "out_time": "0",        //下班已打卡时间
            "mac": [                //可打卡mac地址
                "dc:fe:18:3:26:1a"
            ]
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "打卡失败"    //失败原因
     *  }
     */
    public function actionClock()
    {
        $today = date('Y-m-d');
        $clock = Clock::findOne(['employee_id'=>$this->coachId, 'date'=>$today]);
        $data['in'] = '9:00';
        $data['out'] = '18:00';
        $data['now'] = (string)time();
        $data['in_time'] = $clock['in_time'] ?: '0';
        $data['out_time'] = $clock['out_time'] ?: '0';
        $data['mac'] = ['d8:38:d:2c', 'd8:38:0d:2c'];
        return $data;
    }
    /**
     * @api {get} /service/work/comm-save?accesstoken=666   常用模块添加
     * @apiVersion  1.0.0
     * @apiName        4常用模块添加
     * @apiGroup       work
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/work/clock?accesstoken=666
     * @apiDescription   常用模块添加
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/08
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/work/comm-add?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
    "message": "",
    "code": 1,
    "status": 200,
    "data": {
    "in": "9:00",           //上班时间
    "out": "18:00",         //下班时间
    "now": "1515400947",    //当前时间
    "in_time": "0",         //上班已打卡时间
    "out_time": "0",        //下班已打卡时间
    "mac": [                //可打卡mac地址
    "dc:fe:18:3:26:1a"
    ]
    }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
    "code": 0,
    "status": "error",
    "message": "未选择常用模块！"
    }
     */
    public function actionCommSave()
    {
        $comm = Yii::$app->request->post('comm','0');
        if (empty($comm)) return $this->error('未选择常用模块!');
        if (!is_array($comm)) return $this->error('异常!');
        $model =Employee::findOne($this->employeeId);
        $comm =  json_encode($comm);
        $model->general_module = $comm;
        if ($model->save()){
            return $this->success('分配常用模块成功!');
        }else{
            return $this->error('分配常用模块失败!');
        }
    }
    public function actionIndexnew()
    {
        $data = self::getCommon(
            $this->user->employee->organization->name,
            $this->user->isManager,
            $this->employeeId
        );
        return $this->success($data);
    }

    public function getCommon($organizationName, $isManager, $employeeId)
    {
        $postion =3;
        // if ($this->user->employee->organization->name == '私教部'){
        if ($organizationName == '私教部'){
            // if ($this->user->isManager){
            if ($isManager){
                $postion =1;
            }else{
                $postion=2;
            }
        }
        // if ($this->user->employee->organization->name == '销售部'){
        if ($organizationName == '销售部'){
            $postion =4;
        }
        $all = Yii::$app->request->get('all',0);
        //读取数据库常用模块
        $model = Employee::findOne($employeeId);
        $comm =$model->general_module;
        $array =  Yii::$app->params['general_module'];
        if (empty($comm)){
            $model = Yii::$app->params['position'][$postion];
            $model =  Yii::$app->params[$model]['common'];
            foreach ($model['data'] as $key=>$value){
                $data['common']['title']=$model['title'];
                $data['common'][$value]=$array[$value];
            }
        }else{
            $comm = json_decode($comm);
            foreach ($comm as $key=>$value){
                $data['common']['title']='常用模块';
                $data['common'][$value]=$array[$value];
            }

        }
        //点开的全部模块
        if ($all == 1){
            $model = Yii::$app->params['position'][$postion];
            $model =  Yii::$app->params[$model];
            unset($model['common']);
            foreach ($model as $k=>$value){
                if (empty($value['data'])){
                    unset($model[$k]);
                }
            }
            foreach ($model as $key=>$val){
                $data[$key]['title']=$val['title'];
                foreach ($val['data'] as $value){
                   $data[$key][$value]=$array[$value];
                }
            }
        }
        return $data;
    }
}
