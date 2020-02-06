<?php
namespace coach\controllers;

use coach\base\AuthBaseController;
use common\models\Clock;
use common\models\EntryRecord;
use common\models\AboutClass;

class WorkController extends AuthBaseController
{
    /**
     * @api {get} /coach/work/index?accesstoken=666   工作首页
     * @apiVersion  1.0.0
     * @apiName        工作首页工作首页
     * @apiGroup       work
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/work/index?accesstoken=666
     * @apiDescription   工作首页工作首页
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/work/index?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
     * {
     *    "code": 1,
     *    "status": "success",
     *    "message": "",
     *    "data": {
     *        "pic": "http://oo0oj2qmr.bkt.clouddn.com/5263061513056966.png?e=1513060566&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:0_9FCxb1L5iTVP9h0wqy0ZqTet0=",//banner图片
     *        "news": [                                             //滚动消息
                {
                    "id": "37775",                                  //上课ID
                    "content": "邵乐石预约了12月07日15:30的PT常规课"   //内容
                },
                {
                    "id": "37044",
                    "content": "赵媛媛预约了12月06日19:04的PT常规课"
                },
                {
                    "id": "36780",
                    "content": "邵乐石预约了12月06日16:26的PT常规课"
                }
     *        ],
     *        "num": {
     *            "total": 23,                             //总人数
     *            "man": 4,                                //男
     *           "woman": 18                               //女
     *        }
     *    }
     * }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionIndex()
    {
        $data['pic'] = 'http://oo0oj2qmr.bkt.clouddn.com/5263061513056966.png?e=1513060566&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:0_9FCxb1L5iTVP9h0wqy0ZqTet0=';

        $data['news'] = [];
        //消息
        $models = AboutClass::find()->alias('ac')
            ->joinWith('memberDetails md', FALSE)->joinWith('memberCourseOrderDetails mcod', FALSE)
            ->select('ac.id, md.name, ac.start, mcod.course_name')
            ->where(['coach_id' => $this->coachId])->orderBy('is_read asc, id desc')
            ->andWhere(['ac.status' => 1])
            ->limit(3)->asArray()->all();

        if ($models) {
            foreach ($models as $model) {
                $date           = date('m月d日H:i', $model['start']);
                $data['news'][] = ['id'=>$model['id'], 'content'=>"{$model['name']}预约了{$date}的{$model['course_name']}"];
            }
        }

        //人数统计
        $data['num']['total'] = $data['num']['man'] = $data['num']['woman'] = 0;
        $sexes = EntryRecord::find()->alias('er')->select('md.sex')
            ->innerJoinWith('memberDetails md')->innerJoinWith('aboutClass ac')
            ->where(['ac.coach_id'=>$this->coachId])
            ->andWhere(['between', 'er.entry_time', strtotime(date('Y-m-d')), strtotime(date('Y-m-d').' 23:59:59')])
            ->groupBy('md.member_id')
            ->createCommand()->queryAll();
        if($sexes){
            foreach ($sexes as $sex) $statistics[] = $sex['sex'];
            $statistics = array_count_values($statistics);
            $data['num'] = [
                'total' => array_sum($statistics),
                'man'   => isset($statistics[1]) ? (int)$statistics[1] : 0,
                'woman' => isset($statistics[2]) ? (int)$statistics[2] : 0,
            ];
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

}
