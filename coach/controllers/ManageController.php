<?php
namespace coach\controllers;

use coach\base\AuthBaseController;
use coach\models\Coach;
use coach\models\Sale;
use common\models\AboutClass;
use common\models\Employee;
use common\models\Func;
use common\models\Member;
use common\models\MemberCourseOrder;
//use yii\data\SqlDataProvider;
use coach\base\SqlDataProvider;
use Yii;
use coach\base\ActiveDataProvider;

class ManageController extends AuthBaseController
{

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        if(!$this->user->isManager){
            echo json_encode($this->error('无私教经理权限'));
            exit;
        }
        return $action;
    }

    /**
     * @api {get} /coach/manage/coach-list?accesstoken=666  教练列表
     * @apiVersion  1.0.0
     * @apiName        教练列表
     * @apiGroup       manage
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/manage/coach-list?accesstoken=666
     *   {
     *        "per-page":2              //每页显示数，默认20
     *        "page":2                  //第几页
     *   }
     * @apiDescription   教练列表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/15
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/manage/coach-list?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *

    {
        "code":1,
        "data": [
            {
                "id": "1811",       //教练ID
                "pic": "",          //头像
                "name": "陈鸣芳",    //姓名
                "age": null,        //年龄
                "work_time": 3      //工作年限
            },
            {
                "id": "1674",
                "pic": "",
                "name": "杨东洋",
                "age": null,
                "work_time": null
            }
        ],
        "_links": {
            "self": {
                "href": "http://apiqa.aixingfu.net/coach/manage/coach-list?accesstoken=pcWRga4CUeK6mDZMEYqN90EM8u5TjqLL_1514095464&page=2&per-page=2"
            },
            "first": {
                "href": "http://apiqa.aixingfu.net/coach/manage/coach-list?accesstoken=pcWRga4CUeK6mDZMEYqN90EM8u5TjqLL_1514095464&page=1&per-page=2"
            },
            "prev": {
                "href": "http://apiqa.aixingfu.net/coach/manage/coach-list?accesstoken=pcWRga4CUeK6mDZMEYqN90EM8u5TjqLL_1514095464&page=1&per-page=2"
            },
            "next": {
                "href": "http://apiqa.aixingfu.net/coach/manage/coach-list?accesstoken=pcWRga4CUeK6mDZMEYqN90EM8u5TjqLL_1514095464&page=3&per-page=2"
            },
            "last": {
                "href": "http://apiqa.aixingfu.net/coach/manage/coach-list?accesstoken=pcWRga4CUeK6mDZMEYqN90EM8u5TjqLL_1514095464&page=9&per-page=2"
            }
        },
        "_meta": {
            "totalCount": 236,      //总数
            "pageCount": 118,       //总页数
            "currentPage": 2,       //当前页
            "perPage": 2            //每页显示数
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionCoachList()
    {
        $query = Coach::find()->alias('e')->joinWith('organization o', FALSE)->where(['e.venue_id'=>$this->venueId, 'o.name'=>'私教部', 'e.status'=>1])->orderBy('id desc');

        $provider = new ActiveDataProvider(['query' => $query]);
        return $provider;
    }

    /**
     * @api {post} /coach/manage/assign?accesstoken=666  分配私教
     * @apiVersion  1.0.0
     * @apiName        .分配私教.分配私教
     * @apiGroup       manage
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   POST /coach/manage/assign?accesstoken=666
     *   {
     *        "member_ids": [90117,123,456]    //会员ID数组
     *        "coach_id":1812                  //教练ID
     *   }
     * @apiDescription   分配私教
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/15
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/manage/assign?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code": 1,
        "status": "success",
        "message": "分配成功",
        "data": ""
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "私教不存在"    //失败原因
     *  }
     */
    public function actionAssign()
    {
        $member_ids = Yii::$app->request->post('member_ids', [0]);
        $coach_id = Yii::$app->request->post('coach_id', 0);
        $coach = Employee::find()->alias('e')->joinWith('organization o', FALSE)
            ->where(['e.id'=>$coach_id, 'e.venue_id'=>$this->venueId, 'o.name'=>'私教部', 'e.status'=>1])->one();
        if(!$coach) return $this->error('私教不存在');

        $result = Member::updateAll(['private_id'=>$coach_id], ['id'=>$member_ids, 'venue_id'=>$this->venueId]);

        return $result ? $this->success('', '分配成功') : $this->error('分配失败');
    }

    /**
     * @api {get} /coach/manage/count&accesstoken=666   上课量统计
     * @apiVersion  1.0.0
     * @apiName        教练上课量统计
     * @apiGroup       manage
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/manage/count?accesstoken=666
     *   {
     *        "type":y              //日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     *   }
     * @apiDescription   教练上课量统计
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/15
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/manage/count?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *    "code": 1,                                       //成功返回标识
     *    "status": "success",                             //成功返回标识
     *    "message": "",
     *    "data": [
     *        {
     *            "name": "唐成",                  //教练姓名
     *            "num": "20"                     //上课节数
     *        },
     *        {
     *            "name": "刘琳娜",
     *            "num": "10"
     *        }
     *         "num": 107  总量
     *    ]
     *}
     * @apiSuccessExample {json}返回值详情（失败）
     * {
     *  "code": 0,               //失败返回标识
     *  "status": "error",       //失败返回标识
     *  "message": "暂无数据",   //失败返回标识
     * }
     */
    public function actionCount()
    {
        $type = Yii::$app->request->get('type', 'd');
        $query = AboutClass::find()->alias('ac')->select('e.name, count(*) as num')
            ->joinWith('employee e')
            ->where(['ac.status'=>4, 'ac.type'=>1, 'e.venue_id'=>$this->venueId, 'e.status'=>[1,3]]);

        if(in_array($type, ['d', 'w', 'm', 's', 'y'])){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
            $query->andWhere(['between', 'ac.start', $start, $end]);
        }

        $data =$query->groupBy('ac.coach_id')->createCommand()->queryAll();
            $num=0;
            foreach ($data as $value){
                $num+=$value['num'];
            }
            $datas['num']=$num;
            $datas['data']=$data;
        return $this->success($datas);
    }

    /**
     * @api {get} /coach/manage/table&accesstoken=666   上课量报表
     * @apiVersion  1.0.0
     * @apiName        上课量报表.
     * @apiGroup       manage
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/manage/table?accesstoken=666
     *   {
     *        "start":1501639313              //开始日期(unix时间戳)
     *        "end":1504108800                //结束日期(unix时间戳)
     *        "type":'w'                      //日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     *   }
     * @apiDescription   上课量报表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/15
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/manage/table?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "code": 1,
        "data": [
            {
                "coach_name": "杨东洋",        //教练姓名
                "name": "张章云",              //会员姓名
                "mobile": "13803837888",      //会员手机号
                "num": "1"                    //上课数量
            },
            {
                "coach_name": "李乾坤",
                "name": "康伟",
                "mobile": "18530010725",
                "num": "1"
            }
            "extra": 22,  总结数
        ],
        "_links": {
            "self": {
                "href": "http://apiqa.aixingfu.net/coach/manage/table?accesstoken=pcWRga4CUeK6mDZMEYqN90EM8u5TjqLL_1514095464&start=1&end=12000000000&per-page=2&page=1"
            },
            "next": {
                "href": "http://apiqa.aixingfu.net/coach/manage/table?accesstoken=pcWRga4CUeK6mDZMEYqN90EM8u5TjqLL_1514095464&start=1&end=12000000000&per-page=2&page=2"
            },
            "last": {
                "href": "http://apiqa.aixingfu.net/coach/manage/table?accesstoken=pcWRga4CUeK6mDZMEYqN90EM8u5TjqLL_1514095464&start=1&end=12000000000&per-page=2&page=138"
            }
        },
        "_meta": {
            "totalCount": "275",
            "pageCount": 138,
            "currentPage": 1,
            "perPage": 2
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     * {
     *  "code": 0,               //失败返回标识
     *  "status": "error",       //失败返回标识
     *  "message": "暂无数据",   //失败返回标识
     * }
     */
    public function actionTable()
    {
        $start = Yii::$app->request->get('start', 0);
        $end   = Yii::$app->request->get('end', 0);
        $type  = Yii::$app->request->get('type', 0);
        if(in_array($type, ['d', 'w', 'm', 's', 'y'], TRUE)){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
        }
        $query = AboutClass::find()->alias('ac')->select('e.name as coach_name, md.name, m.mobile, count(*) as num')
            ->joinWith('employee e')->joinWith('member m')->joinWith('memberDetails md')
            ->where(['ac.status' => 4, 'ac.type' => 1, 'e.venue_id' => $this->venueId, 'e.status' => 1]);

        if($start && $end) $query->andWhere(['between', 'ac.start', $start, $end]);
//        $query->groupBy('ac.member_id, ac.coach_id')->orderBy('ac.id desc');

        $sql = $query->groupBy('ac.member_id, ac.coach_id')->orderBy('ac.id desc')->createCommand()->getRawSql();
        $provider = new SqlDataProvider(['sql'=>$sql ,'pagination' => array('pageSize' => false)]);
        $num =0;
        foreach ($provider->getModels() as $v){
            $num+=$v['num'];
        }
        $providers = new SqlDataProvider(['sql'=>$sql,'extra'=>['sum'=>$num]]);
        return $providers;
    }

    /**
     * @api {get} /coach/manage/sale&accesstoken=666   业绩报表
     * @apiVersion  1.0.0
     * @apiName        业绩报表.
     * @apiGroup       manage
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/manage/sale?accesstoken=666
     *   {
     *        "start":1501639313              //开始日期(unix时间戳)
     *        "end":1504108800                //结束日期(unix时间戳)
     *        "type":"y"                      //日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     *   }
     * @apiDescription   业绩报表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/15
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/manage/sale?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "code": 1,
        "data": [
            {
                "coach_name": "冯帅旗",              //教练姓名
                "name": "袁帅",                      //会员姓名
                "status": "成交",                    //状态
                "product_name": "MFT格斗健身课程",    //课程名称
                "course_amount": 10,                //课程节数
                "money_amount": "3300.00"           //成交金额
            },
            {
                "coach_name": "冯帅旗",
                "name": "李文慧",
                "status": "成交",
                "product_name": "PT常规课",
                "course_amount": 5,
                "money_amount": "1450.00"
            }
        "extra": {
                "sum": "18690.00"  //总金额
            },
        ],
        "_links": {
            "self": {
                "href": "http://apiqa.aixingfu.net/coach/manage/sale?accesstoken=pcWRga4CUeK6mDZMEYqN90EM8u5TjqLL_1514095464&start=1&end=12000000000&per-page=2&page=1"
            },
            "next": {
                "href": "http://apiqa.aixingfu.net/coach/manage/sale?accesstoken=pcWRga4CUeK6mDZMEYqN90EM8u5TjqLL_1514095464&start=1&end=12000000000&per-page=2&page=2"
            },
            "last": {
                "href": "http://apiqa.aixingfu.net/coach/manage/sale?accesstoken=pcWRga4CUeK6mDZMEYqN90EM8u5TjqLL_1514095464&start=1&end=12000000000&per-page=2&page=658"
            }
        },
        "_meta": {
            "totalCount": 1315,
            "pageCount": 658,
            "currentPage": 1,
            "perPage": 2
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     * {
     *  "code": 0,               //失败返回标识
     *  "status": "error",       //失败返回标识
     *  "message": "暂无数据",   //失败返回标识
     * }
     */
    public function actionSale()
    {
        $start = Yii::$app->request->get('start', 0);
        $end   = Yii::$app->request->get('end', 0);
        $type  = Yii::$app->request->get('type', 0);
        if(in_array($type, ['d', 'w', 'm', 's', 'y'], TRUE)){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
        }
        $query = Sale::find()->alias('mco')
            ->joinWith('employeeS e')
            ->where(['e.venue_id'=>$this->venueId]);

        if($start && $end) $query->andWhere(['between', 'mco.create_at', $start, $end]);

        $query->orderBy('mco.id desc');
        $querySum = clone $query;
        return new ActiveDataProvider(['query' => $query,'extra'=>['sum'=>$querySum->sum('mco.money_amount')]]);
    }
}
