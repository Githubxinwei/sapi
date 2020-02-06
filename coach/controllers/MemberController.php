<?php
namespace coach\controllers;

use coach\base\AuthBaseController;
use coach\models\Member;
use coach\models\MemberClass;
use common\models\Func;
use yii\data\ActiveDataProvider;
use Yii;
use common\models\base\Employee;
use common\models\base\MemberCourseOrder;
use common\models\base\MemberCourseOrderDetails;

class MemberController extends AuthBaseController
{

    /**
     * @api {get} /coach/member/index?accesstoken=666  会员列表/会员搜索
     * @apiVersion  1.0.0
     * @apiName        会员列表
     * @apiGroup       member
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/member/index?accesstoken=666
     *   {
     *        "manage":0                //(可选)团队会员列表:/coach/member/index?accesstoken=666&manage=1
     *        "assign":0                //(可选)未购课的待分配教练会员列表:/coach/member/index?accesstoken=666&assign=1
     *        "per-page":2              //每页显示数，默认20
     *        "page":2                  //第几页
     *        "keyword"                 //搜索条件（会员姓名/手机号）/coach/member/index?accesstoken=666&assign=1&keyword=username/
     *   }
     * @apiDescription   会员列表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/13
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/member/index?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code":1,
        "data": [
            {
                "id": "48324",                         //会员ID
                "pic": "http://oo0oj2qmr.bkt.clouddn.com/20700255.JPG?e=1501923581&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:E1PpeGYJWxXfBO8aQzrujopiZ-4=",//头像
                "name": "马婷婷",                      //姓名
                "sex": "女",                          //性别
                "age": null,                          //年龄
                "mobile": "13783592200",              //手机号码
                "id_card": "413026198606210029"       //身份证号
            },
            {
                "id": "50060",
                "pic": null,
                "name": "催佳慧",
                "sex": "女",
                "age": null,
                "mobile": "13837134704",
                "id_card": null
            }
        ],
        "_links": {
            "self": {
                "href": "http://apiqa.aixingfu.net/coach/member/index?accesstoken=9Bjx_a8hxzh4DKeQzqrscp3h5g6TeL8Y&per-page=2&page=11"
            },
            "first": {
                "href": "http://apiqa.aixingfu.net/coach/member/index?accesstoken=9Bjx_a8hxzh4DKeQzqrscp3h5g6TeL8Y&per-page=2&page=1"
            },
            "prev": {
                "href": "http://apiqa.aixingfu.net/coach/member/index?accesstoken=9Bjx_a8hxzh4DKeQzqrscp3h5g6TeL8Y&per-page=2&page=10"
            },
            "next": {
                "href": "http://apiqa.aixingfu.net/coach/member/index?accesstoken=9Bjx_a8hxzh4DKeQzqrscp3h5g6TeL8Y&per-page=2&page=12"
            },
            "last": {
                "href": "http://apiqa.aixingfu.net/coach/member/index?accesstoken=9Bjx_a8hxzh4DKeQzqrscp3h5g6TeL8Y&per-page=2&page=12"
            }
        },
        "_meta": {
            "totalCount": 24,
            "pageCount": 12,
            "currentPage": 11,
            "perPage": 2
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
        $manage = Yii::$app->request->get('manage', 0);
        $assign = Yii::$app->request->get('assign', 0);
        $keyword = Yii::$app->request->get('keyword','');
        $query = Member::find()->alias('m')
            ->joinWith('memberCourseOrder mco', FALSE)
            ->where(['mco.private_id' => $this->coachId])
            ->orWhere(['m.private_id'=>$this->coachId])
            ->groupBy('m.id')->orderBy('mco.id desc');

        //团队会员
        if($manage && $this->user->isManager){
            $query = Member::find()->where(['venue_id'=>$this->venueId])->orderBy('id desc');
        }

        //未购课的待分配教练会员
        if($assign){
            if(!$this->user->isManager) return $this->error('无私教经理权限');
            $query = Member::find()->alias('m')
                ->joinWith('memberCourseOrder mco', FALSE)
                ->joinWith('memberDetails md')
                ->where(['m.venue_id'=>$this->venueId, 'm.private_id'=>0, 'mco.member_id'=>NULL]);
            if($keyword) $query->andWhere(['or', ['like', 'md.name', $keyword], ['like', 'm.mobile', $keyword],['like', 'md.member_id', $keyword]]);
            $query->orderBy('m.id desc');
        }

        $provider = new ActiveDataProvider(['query' => $query]);
        return $provider;
    }


    /**
     * @api {get} /coach/member/view?accesstoken=666   会员详情
     * @apiVersion  1.0.0
     * @apiName        .会员详情
     * @apiGroup       member
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/member/view?accesstoken=666
     * {
     *        "id":"48324"，             //会员ID
     * }
     * @apiDescription   会员详情
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/13
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/member/view?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code": 1,
        "status": "success",
        "message": "",
        "data": {
            "id": "48324",                         //会员ID
            "pic": "http://oo0oj2qmr.bkt.clouddn.com/20700255.JPG?e=1501923581&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:E1PpeGYJWxXfBO8aQzrujopiZ-4=",//头像
            "name": "马婷婷",                      //姓名
            "sex": "女",                          //性别
            "age": null,                          //年龄
            "mobile": "13783592200",              //手机号码
            "id_card": "413026198606210029"       //身份证号
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
/*        $model = Member::find()->alias('m')
            ->joinWith('memberCourseOrder mco', FALSE)
            ->where(['m.id'=>$id, 'mco.private_id' => $this->coachId])
            ->one();

        //私教经理可查看
        if($this->user->isManager){
            $model = Member::find()->where(['id'=>$id, 'venue_id'=>$this->venueId])->one();
        }*/
        $model = Member::find()->where(['id'=>$id, 'company_id'=>$this->companyId])->one();

        if(!$model) return $this->error('不存在或无权限');

        return $this->success($model);
    }

    /**
     * @api {get} /coach/member/course-list?id=92447&accesstoken=666   私教列表
     * @apiVersion  1.0.0
     * @apiName        .私教列表.
     * @apiGroup       member
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/member/course-list?id=92447&accesstoken=666
     * {
     *        "id":"30018"，             //会员ID
     * }
     * @apiDescription   私教列表
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsports.club
     * <span><strong>创建时间：</strong></span>2017/12/14
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/member/course-list?id=92447&accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
     *{
     *    "code": 1,
     *    "status": "success",
     *    "message": "",
     *    "data": [
     *        {
     *        "id": "50744",                 //私教课订单id
     *        "product_name": "PT常规课",    //私教课程名字
     *        "overage_section": "0"         //剩余节数
     *        }
     *    ]
     *}
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                           //失败表示
     *      "status": "error",                   //请求状态
     *      "message": "该会员还没有购买课程"    //失败原因
     *  }
     */
    public function actionCourseList($member_id)
    {
        $query = MemberCourseOrder::find()->where(['member_id'=>$member_id])->select(['id','product_name','overage_section'])->asArray();

        $provider = new ActiveDataProvider(['query' => $query]);
        return $provider;

/*        $data = MemberCourseOrder::find()->where(['member_id'=>$id])->select(['id','product_name','overage_section'])->asArray()->all();
        if(!$data){
            return $this->error('该会员还没有购买课程');
        } else {
            return $this->success($data);
        }*/
    }

    /**
     * @api {get} /coach/member/course-info?id=50744&accesstoken=666   私教详情
     * @apiVersion  1.0.0
     * @apiName        .私教详情.私教详情
     * @apiGroup       member
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/member/course-info?id=50744&accesstoken=666
     * {
     *        "id":"50744"，             //私教订单ID
     * }
     * @apiDescription   私教详情
     * <br/>
     * <span><strong>作    者：</strong></span>焦冰洋<br/>
     * <span><strong>邮    箱：</strong></span>jiaobingyang@itsports.club
     * <span><strong>创建时间：</strong></span>2017/12/14
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/member/course-info?id=50744&accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
     *{
     *    "code": 1,
     *    "status": "success",
     *    "message": "",
     *    "data": {
     *        "product_name": "PT常规课",    //课程名称
     *        "seller": "邹恒",              //卖课教练
     *        "buy_time": "2017-12-25 13:02",//卖课时间
     *        "money_amount": "0.00",        //办理金额
     *        "class_length": 60,            //课程时长（分钟）
     *        "course_amount": 3,            //总节数
     *        "overage_section": "0",        //剩余节数
     *        "invalid_time": "2018-12-18"   //到期日期
     *    }
     *}
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                        //失败表示
     *      "status": "error",                //请求状态
     *      "message": "无此课程"             //失败原因
     *  }
     */
    public function actionCourseInfo($id)
    {
/*        $data = MemberCourseOrder::findOne(['id'=>$id]);
        if($data){
            $details = MemberCourseOrderDetails::findOne(['course_order_id'=>$id]);
            if($details){
                $seller = Employee::findOne(['id'=>$data->seller_id]);
                if($seller){
                    $info = [
                        'product_name'   => $data->product_name,
                        'seller'         => $seller->name,
                        'buy_time'       => date('Y-m-d H:i', $data->create_at),
                        'money_amount'   => $data->money_amount,
                        'class_length'   => $details->class_length,
                        'course_amount'  => $data->course_amount,
                        'overage_section'=> $data->overage_section,
                        'invalid_time'   => date('Y-m-d', $data->deadline_time),
                    ];
                    return $this->success($info);
                }
            }
        }else{
            return $this->error('无此课程');
        }*/
        $model = MemberCourseOrder::findOne($id);
        if(!$model) return $this->error('无此课程');

        $info = [
            'product_name'   => $model->product_name,
            'seller'         => Func::getRelationVal($model, 'employee', 'name'),
            'buy_time'       => date('Y-m-d H:i', $model->create_at),
            'money_amount'   => $model->money_amount,
            'class_length'   => Func::getRelationVal($model, 'memberCourseOrderDetails', 'class_length'),
            'course_amount'  => $model->course_amount,
            'overage_section'=> $model->overage_section,
            'invalid_time'   => date('Y-m-d', $model->deadline_time),
        ];
        return $this->success($info);
    }


    /**
     * @api {get} /coach/member/class?accesstoken=666  会员列表带上课信息
     * @apiVersion  1.0.0
     * @apiName        5会员列表带上课信息
     * @apiGroup       member
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/member/class?accesstoken=666
     *   {
     *        "keyword":"137"           //关键词
     *        "per-page":2              //每页显示数，默认20
     *        "page":2                  //第几页
     *   }
     * @apiDescription   会员列表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/02/05
     * @apiSampleRequest  http://apiqa.aixingfu.net/coach/member/class?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code": 1,
        "data": [
            {
                "id": "67257",//会员ID
                "pic": "http://oo0oj2qmr.bkt.clouddn.com/1115431511268472.png?e=1511272072&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:pKUyW3wOC1lCni4HKFFw34u2aPw=",
                "name": "杨霞",
                "class_info": {
                    "id": "93428",//上课ID
                    "member_id": "67257",
                    "card_number": "10100882",
                    "start": "01月25日 13:00",
                    "status": 4,
                    "is_read": 1,
                    "name": "杨霞",
                    "coach_name": "唐成",
                    "pic": "http://oo0oj2qmr.bkt.clouddn.com/1115431511268472.png?e=1511272072&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:pKUyW3wOC1lCni4HKFFw34u2aPw=",
                    "course_name": "PT游泳课",
                    "class_length": "40",
                    "time": "12:48",
                    "class_date": "2018-01-25"
                }
            },
            {
                "id": "88588",
                "pic": "",
                "name": "饶梅",
                "class_info": null
            },
            {
                "id": "48312",
                "pic": "http://oo0oj2qmr.bkt.clouddn.com/7594961509952993.JPG?e=1509956593&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:agj883iPKAfEgl-5F4tvL30jNNc=",
                "name": "王晓莹",
                "class_info": {
                    "id": "58064",
                    "member_id": "48312",
                    "card_number": "20700221",
                    "start": "12月26日 15:00",
                    "status": 4,
                    "is_read": 0,
                    "name": "王晓莹",
                    "coach_name": "唐成",
                    "pic": "http://oo0oj2qmr.bkt.clouddn.com/7594961509952993.JPG?e=1509956593&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:agj883iPKAfEgl-5F4tvL30jNNc=",
                    "course_name": "PT游泳课",
                    "class_length": "40",
                    "time": "14:58",
                    "class_date": "2017-12-26"
                }
            }
        ],
        "_links": {
            "self": {
                "href": "http://127.0.0.3/coach/member/class?accesstoken=001_1544587933&per-page=3&page=2"
            },
            "first": {
                "href": "http://127.0.0.3/coach/member/class?accesstoken=001_1544587933&per-page=3&page=1"
            },
            "prev": {
                "href": "http://127.0.0.3/coach/member/class?accesstoken=001_1544587933&per-page=3&page=1"
            },
            "next": {
                "href": "http://127.0.0.3/coach/member/class?accesstoken=001_1544587933&per-page=3&page=3"
            },
            "last": {
                "href": "http://127.0.0.3/coach/member/class?accesstoken=001_1544587933&per-page=3&page=73"
            }
        },
        "_meta": {
            "totalCount": 217,
            "pageCount": 73,
            "currentPage": 2,
            "perPage": 3
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionClass()
    {
        $keyword = Yii::$app->request->get('keyword', '');
        $manage = Yii::$app->request->get('manage', 0);
        $query = MemberClass::find()->alias('m')
            ->joinWith('memberCourseOrder mco', FALSE)
            ->where(['mco.private_id' => $this->coachId])
            ->orWhere(['m.private_id'=>$this->coachId])
            ->groupBy('m.id')->orderBy('mco.id desc');

        //团队会员
        if($manage && $this->user->isManager){
            $query = MemberClass::find()->alias('m')->where(['m.venue_id'=>$this->venueId])->orderBy('id desc');
        }

        if($keyword) $query->joinWith('memberDetails md')->andWhere(['or', ['like', 'm.mobile', $keyword], ['like', 'm.id', $keyword], ['like', 'md.name', $keyword]]);

        $provider = new ActiveDataProvider(['query' => $query]);
        return $provider;
    }

}
