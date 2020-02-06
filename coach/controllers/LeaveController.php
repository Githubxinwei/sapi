<?php
namespace coach\controllers;

use coach\base\AuthBaseController;
use coach\models\LeaveRecordIndex;
use coach\models\LeaveRecordView;
use Yii;
use yii\data\ActiveDataProvider;

class LeaveController extends AuthBaseController
{
    /**
     * @api {get} /coach/leave/index?accesstoken=666   请假列表
     * @apiVersion  1.0.0
     * @apiName        请假列表
     * @apiGroup       leave
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/leave/index?accesstoken=666
     *   {
     *        "member_id": "62226"       //会员请假记录 例：会员ID为62226的请假列表 /coach/leave/index?accesstoken=666&member_id=62226
     *        "per-page":2               //每页显示数，默认20
     *        "page":2                   //第几页
     *   }
     * @apiDescription   请假列表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/13
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/leave/index?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "code":1,
        "data": [
            {
                "id": "337",                    //请假ID，获取请假详情用此ID /coach/leave/view?accesstoken=666&id=337
                "member_id": "62448",           //会员ID
                "card_number": "61000185",      //会员卡号
                "name": "贾利玲",                //会员姓名
                "leave_property": 2,            //请假类型1.特殊请假2.正常请假3.学生请假
                "leave_end_time": "12月13日",    //结束日期
                "is_read": 0,                   //是否已读 0未读 1已读
                "time": "19:03"                 //提交时间
                "leave_length": "30",           //请假天数
            },
            {
                "id": "327",
                "member_id": "62432",
                "card_number": "61000185",
                "name": "潘梦娇",
                "leave_property": 2,
                "leave_end_time": "12月12日",
                "is_read": 0,
                "time": "19:03"
                "leave_length": "30",
            }
        ],
        "_links": {
            "self": {
                "href": "http://apiqa.aixingfu.net/coach/leave/index?accesstoken=GqK3hyjiw4yYtETRMibdr8z77666ocIz&per-page=2&page=2"//当前页
            },
            "first": {
                "href": "http://apiqa.aixingfu.net/coach/leave/index?accesstoken=GqK3hyjiw4yYtETRMibdr8z77666ocIz&per-page=2&page=1"//第一页
            },
            "prev": {
                "href": "http://apiqa.aixingfu.net/coach/leave/index?accesstoken=GqK3hyjiw4yYtETRMibdr8z77666ocIz&per-page=2&page=1"//上一页
            },
            "next": {
                "href": "http://apiqa.aixingfu.net/coach/leave/index?accesstoken=GqK3hyjiw4yYtETRMibdr8z77666ocIz&per-page=2&page=3"//下一页
            },
            "last": {
                "href": "http://apiqa.aixingfu.net/coach/leave/index?accesstoken=GqK3hyjiw4yYtETRMibdr8z77666ocIz&per-page=2&page=7"//最后页
            }
        },
        "_meta": {
            "totalCount": 13,       //总数
            "pageCount": 7,         //总页数
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
    public function actionIndex()
    {
        $member_id = Yii::$app->request->get('member_id', 0);

        $query = LeaveRecordIndex::find()->alias('lr')
            ->joinWith('memberCourseOrder mco', FALSE)
            ->where(['mco.private_id'=>$this->coachId]);

        if($member_id) $query->andWhere(['mco.member_id'=>$member_id]);//会员的
        $query->orderBy('id desc');
        $provider = new ActiveDataProvider(['query' => $query]);
        return $provider;
    }

    /**
     * @api {get} /coach/leave/view?accesstoken=666   请假详情
     * @apiVersion  1.0.0
     * @apiName        .请假详情.
     * @apiGroup       leave
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/leave/view?accesstoken=666
     * {
     *        "id":"48324"，             //请假ID
     * }
     * @apiDescription   请假详情
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/13
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/leave/view?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code": 1,
        "status": "success",
        "message": "",
        "data": {
            "id": "634",                            //请假ID
            "member_id": "62213",                   //会员ID
            "name": "张若鑫",                        //会员姓名
            "pic": "http://oo0oj2qmr.bkt.clouddn.com/493781506316094.JPG?e=1506319694&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:dsb5wPUdvHoQF74WdfhSjgL4ypQ=",//会员头像
            "sex": "女",                            //性别
            "age": 29,                              //年龄
            "mobile": "18703656666",                //手机
            "member_card": "WD T24MD",              //会员卡名称
            "card_number": "61000066",              //会员卡号
            "leave_property": 2,                    //请假类型1.特殊请假2.正常请假3.学生请假
            "leave_remain": "2次",                  //剩余几次或几天
            "leave_start_time": "2017-12-06",       //开始日期
            "leave_end_time": "2018-01-04",         //结束日期
            "leave_length": "30",                   //请假天数
            "note": "事假.顾问郭强"                  //请假原因
            "type":1                                //状态1待处理,2已同意,3已拒绝
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionView($id)
    {
        $model = LeaveRecordView::find()->alias('lr')
            ->joinWith('memberCourseOrder mco', FALSE)
            ->where(['lr.id'=>$id, 'mco.private_id'=>$this->coachId])
            ->one();
        if(!$model) return $this->error('不存在或无权限');

        $model->is_read = 1;
        $model->save();
        return $this->success($model);
    }


}
