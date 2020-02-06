<?php
namespace service\controllers;

use service\models\LeaveRecordIndex;
use service\models\LeaveRecordList;
use service\models\LeaveRecordView;
use service\base\BaseController;
use service\models\Member;
use Yii;
use yii\data\ActiveDataProvider;

class LeaveController extends BaseController
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
        // echo 1;die;21732
        $member_id = Yii::$app->request->get('member_id', 0);
        // $member_id = '';
        if(!empty($member_id))
        {
            $query = LeaveRecordIndex::find()
            ->alias('lr')
            ->joinWith('member m', FALSE)
            // ->where(['mco.private_id'=>$this->coachId]);              
            ->Where(['m.id'=>$member_id])
            ->orderBy('is_read asc,id desc');
            $provider = new ActiveDataProvider(['query' => $query]);
        }
        else
        {

            $query = LeaveRecordIndex::find()->alias('lr')
                ->joinWith('memberCourseOrder mco', FALSE)
                ->where(['mco.private_id'=>$this->coachId]);
                if($member_id) $query->andWhere(['mco.member_id'=>$member_id]);//会员的
                $query->orderBy('is_read asc,id desc');
            $provider = new ActiveDataProvider(['query' => $query]);


        }
      
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
     *        "type"     1特殊请假 2不同请假  详情
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
        $type =  Yii::$app->request->get('type', 2);
        if($type==1){
            $model = LeaveRecordView::find()->alias('lr')
                ->joinWith('memberCourseOrder mco', FALSE)
                ->where(['lr.id'=>$id])
                ->one();
            if(!$model) return $this->error('不存在或无权限');
            $model->is_read = 1;
        }else{
            $model = LeaveRecordView::find()->alias('lr')
                ->joinWith('memberCourseOrder mco', FALSE)
                ->where(['lr.id'=>$id, 'mco.private_id'=>$this->coachId])
                ->one();
            if(!$model) return $this->error('不存在或无权限');

            $model->is_read = 1;
            $model->save();
        }


        return $this->success($model);
    }
    /**
     * @api {get} /service/leave/list?accesstoken=666   12特殊请假列表
     * @apiVersion  1.0.0
     * @apiName        12特殊请假列表
     * @apiGroup       leave
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/leave/index?accesstoken=666
     *   {
     *        "keyword": "62226"       //搜索关键词 姓名/手机号
     *        "type"                   //1 未处理 2 已处理
     *   }
     * @apiDescription   12特殊请假列表
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/13
     * @apiSampleRequest  http://qaservice.xingfufit.cn/service/leave/index?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
    "code": 1,
    -"data": [
    -{
    "id": "4795",
    "member_id": "98544",
    "card_number": "09802599",
    "name": "陈杨",                   //姓名
    "leave_property": 1,
    "leave_end_time": "04月13日",
    "is_read": 1,
    "time": "19:55",                  //时间
    "create_at": "2018年04月12日",    //申请时间
    "leave_length": "2"              //请假天数
    }
    ],
    -"_links": {
    -"self": {
    "href": "http://192.168.6.48/service/leave/list?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&type=1&keyword=%E9%99%88%E6%9D%A8&page=1"
    }
    },
    -"_meta": {
    "totalCount": 2,
    "pageCount": 1,
    "currentPage": 1,
    "perPage": 20
    }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionList()
    {
        $type =  Yii::$app->request->get('type', 1);
        $keyword = Yii::$app->request->get('keyword', '');
        if (!in_array($type,[1,2])) return $this->error('参数异常!');
        $type = empty($keyword) ? $type == 1 ? 1 :[2,3]:[1,2,3];
        $query = LeaveRecordList::find()->alias('lr')
            ->joinWith('memberCourseOrder mco',FALSE)
           ->andwhere(['lr.leave_property'=>1,'lr.type'=>$type])->joinWith('memberDetails md')->joinWith('member m')->andWhere(['m.venue_id'=>$this->venueId]);
        if($keyword) $query->andWhere(['or', ['like', 'md.name', $keyword], ['like', 'm.mobile', $keyword]]);
        $query->orderBy('create_at desc,is_read Desc');
        $provider = new ActiveDataProvider(['query' => $query]);
        return $provider;
    }
    /**
     * @api {get} /service/leave/Approval?accesstoken=666   特殊请假审批
     * @apiVersion  1.0.0
     * @apiName        .特殊请假审批.
     * @apiGroup       leave
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   post /coach/leave/view?accesstoken=666
     * {
     *        "id":"48324"，             //请假ID
     *        "type"  1 同意 2 拒绝
     *        "reject_note"   拒绝原因
     * }
     * @apiDescription   请假详情
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/13
     * @apiSampleRequest  http://qaservice.xingfufit.cn/service/leave/Details?accesstoken=666
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
    public function actionApproval()
    {
        $post  = \Yii::$app->request->post();
        if (!isset($post['id'])) return $this->error('请选择审批！');
        $Leave = LeaveRecordView::findOne($post['id']);
        if (empty($Leave)) return $this->error('不存在或无权限');
        if ($Leave->type !==1) return $this->error('已审批,请勿重复审批!');
        if ($post['type'] == 1){
//            $Leave->is_approval=true;
            $Leave->is_read=0;
            $Leave->type=2;
            $Leave->status = 1;
            $Leave->is_approval_id= $this->employeeId;
            $msg = "同意请假申请!";
        }else{
            if (empty($post['reject_note'])) return $this->error('拒绝原因必填!');
//            $Leave->is_approval=false;
            $Leave->is_read=0;
            $Leave->type=3;
            $Leave->status = 2;
            $Leave->is_approval_id= $this->employeeId;
            $Leave->reject_note = $post['reject_note'];
            $msg = "拒绝请假申请!";
        }
        if ($Leave->save()){
            return $this->success($msg);
        }else{
            return $Leave->errors;
        }

    }

}
