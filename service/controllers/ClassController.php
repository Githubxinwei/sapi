<?php
namespace service\controllers;

use service\models\AboutClassIndex;
use service\models\AboutClassView;
use service\models\Coach;
use Codeception\Module\REST;
use common\models\AboutClass;
use common\models\Admin;
use common\models\base\Auth;
use common\models\base\Employee;
use common\models\Clock;
use common\models\Func;
use common\models\MemberCourseOrder;
use common\models\MemberCourseOrderDetails;
use service\base\BaseController;
use service\models\MemberClassBeforeQuestion;
use Yii;
use yii\data\ActiveDataProvider;

class ClassController extends BaseController
{

    /**
     * @api {get} /service/class/index?accesstoken=666   上课列表/上课搜索
     * @apiVersion  1.0.0
     * @apiName        上课列表/上课搜索
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/class/index?accesstoken=666
     *   {
     *        "status":"1"，             //上课状态（1:未上课 2:取消预约 3:上课中 4:下课 5:过期（爽约）） 例：已下课列表 /coach/class/index?accesstoken=666&status=4
     *        "keyword":"张"             //上课搜索关键词  上课搜索接口调用 例如：/coach/class/index?accesstoken=666&keyword=邵乐石
     *        "member_id": "62226"       //会员上课记录，取消记录，过期（爽约）记录 例：会员ID为62226的取消列表 /coach/class/index?accesstoken=666&member_id=62226&status=2
     *        "per-page":2               //每页显示数，默认20
     *        "page":2                   //第几页
     *   }
     * @apiDescription   上课列表/上课搜索/会员的上课列表
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/class/index?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code":1,
        "data": [
            {
                "id": "36780",                  //上课ID,获取上课详情用此ID /coach/class/view?accesstoken=666&id=36780
                "member_id": "62226",           //会员ID
                "card_number": "42848",         //会员卡号
                "start": "12月06日 16:26",       //上课开始时间
                "status": "4",                  //上课状态（1:未上课 2:取消预约 3:上课中 4:下课 5:过期）
                "is_read": "0",                 //是否已读（0未读1已读）
                "name": "邵乐石",                //会员姓名
                "coach_name": "冯帅旗",          //教练姓名
                "pic": "http://oo0oj2qmr.bkt.clouddn.com/1550081506425328.JPG?e=1506428928&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:QpqgwNb09s87KQ-OXdU4pMFvmC0=",//会员头像
                "course_name": "PT常规课",       //课程名称
                "class_length": "60",           //课程时长
                "time": "19:03"                 //预约时间
            },
            {
                "id": "36668",
                "member_id": "61528",
                "card_number": "65928",
                "start": "12月06日 16:26",
                "status": "4",
                "is_read": "0",
                "name": "秦亚坤",
                "coach_name": "冯帅旗",
                "pic": "http://oo0oj2qmr.bkt.clouddn.com/6914541505644993.JPG?e=1505648593&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:G4NtiQPzYoOAieb9CsOUQ-az3AE=",
                "course_name": "PT常规课",
                "class_length": "60",
                "time": "19:03"
            }
        ],
        "_links": {
            "self": {
                "href": "http://apiqa.aixingfu.net/coach/class/index?accesstoken=666&per-page=2&page=2"//当前页
            },
            "first": {
                "href": "http://apiqa.aixingfu.net/coach/class/index?accesstoken=666&per-page=2&page=1"//第一页
            },
            "prev": {
                "href": "http://apiqa.aixingfu.net/coach/class/index?accesstoken=666&per-page=2&page=1"//上一页
            },
            "next": {
                "href": "http://apiqa.aixingfu.net/coach/class/index?accesstoken=666&per-page=2&page=3"//下一页
            },
            "last": {
                "href": "http://apiqa.aixingfu.net/coach/class/index?accesstoken=666&per-page=2&page=118"//最后页
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
     public function actionIndex()

    {
        $status = Yii::$app->request->get('status',0);
        $keyword = Yii::$app->request->get('keyword','');
        // $member_id = 21770;
        $manage = Yii::$app->request->get('manage', 0);
        $coach_id = Yii::$app->request->get('coach_id', $this->coachId);
        $date = Yii::$app->request->get('date', 0);
        // $status = 4;
        $member_id = Yii::$app->request->get('member_id', 0);
        // $member_id = '';
        if(!empty($member_id))
        {
            $query = AboutClassIndex::find()->alias('ac')
            ->joinWith('member m', FALSE)->joinWith('memberDetails md', FALSE)
            ->where(['ac.type'=>1]);

            // if($manage && $this->user->isManager){
            //     $query->joinWith('employee e', FALSE)->andWhere(['e.venue_id'=>$this->venueId]);
            // }else{
            //     $query->andWhere(['ac.coach_id'=>$coach_id]);
            // }

            if($date) $query->andWhere(['ac.class_date'=>$date]);
            if(in_array($status, [1,2,3,4,5,6])) $query->andWhere(['ac.status'=>$status]);
            if($keyword) $query->andWhere(['or', ['like', 'm.mobile', $keyword], ['like', 'md.id', $keyword], ['like', 'md.name', $keyword]]);//上课搜索
            if($member_id) $query->andWhere(['ac.member_id'=>$member_id]);//会员的
            $query->orderBy('ac.is_read asc, ac.class_date desc');

            $provider = new ActiveDataProvider(['query' => $query]);
        }
        else
        {
            // echo 1;die;
            $query = AboutClassIndex::find()->alias('ac')
            ->joinWith('member m', FALSE)->joinWith('memberDetails md', FALSE)
            ->where(['ac.type'=>1]);

            if($manage && $this->user->isManager){
                $query->joinWith('employee e', FALSE)->andWhere(['e.venue_id'=>$this->venueId]);
            }else{
                $query->andWhere(['ac.coach_id'=>$coach_id]);
            }
// var_dump($this->user->isManager);die;
            if($date) $query->andWhere(['ac.class_date'=>$date]);
            if(in_array($status, [1,2,3,4,5,6])) $query->andWhere(['ac.status'=>$status]);
            if($keyword) $query->andWhere(['or', ['like', 'm.mobile', $keyword], ['like', 'md.id', $keyword], ['like', 'md.name', $keyword]]);//上课搜索
            if($member_id) $query->andWhere(['ac.member_id'=>$member_id]);//会员的
            $query->orderBy('ac.is_read asc, ac.class_date desc');

            $provider = new ActiveDataProvider(['query' => $query]);
        }

        
        return $provider;
    }


    /**
     * @api {get} /service/class/view?accesstoken=666   上课详情
     * @apiVersion  1.0.0
     * @apiName        上课详情
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/class/view?accesstoken=666
     * {
     *        "id":"30018"，             //上课ID
     * }
     * @apiDescription   上课详情
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/class/view?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code": 1,
        "status": "success",
        "message": "",
        "data": {
            "id": "30018",                  //上课ID
            "member_id": "48324",           //会员ID
            "pic": "http://oo0oj2qmr.bkt.clouddn.com/20700255.JPG?e=1501923581&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:E1PpeGYJWxXfBO8aQzrujopiZ-4=",//会员头像
            "name": "马婷婷",                //会员姓名
            "sex": "女",                    //性别
            "age": "",                      //年龄（数据库中有的出生日期未填写）
            "mobile": "13783592200",        //手机
            "course_name": "PT常规课",       //课程名称
            "product_name": "PT常规课",      //课种名称
            "coach": "冯帅旗",               //教练
            "start": "11月29日 12:30",       //开课时间
            "class_length": 60,             //课程时长
            "card_name": "两年白金瑜伽卡",    //会员卡名
            "card_number": "20700255",      //会员卡号
            "sell_coach": "冯帅旗",          //办理私教
            "money_amount": 0,              //课程金额
            "course_amount": 26,            //总节数
            "overage_section": "",          //剩余节数
            "deadline_time": "2019-08-10",  //到期日期
            "cancel_time": "",              //取消时间（取消详情使用，其他状态可忽略)
            "cancel_reason": "",            //取消原因（取消详情使用，其他状态可忽略)
            "month_cancel": 0               //本月取消次数（取消详情使用，其他状态可忽略)
		    Inquiries: [
			    {
				    id: "64",
				    title: "如何锻炼？", //问题名称
				    type: 2,            //类型 1 自定义答案, 2 多选, 3 单选
				    option: [
					    "A:深蹲",
					    "B:举重",
					    "C:跑步",
					    "D:弹跳",
					    ],
			    },
		    ],
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
        $model = AboutClassView::findOne(['id'=>$id]);
        if(!$model) return $this->error('不存在');
        if($model->coach_id == $this->coachId){
            $model->is_read = 1;
            $model->save();
        }
        return $this->success($model);
    }


    /**
     * @api {get} /service/class/in?accesstoken=666   上课打卡
     * @apiVersion  1.0.0
     * @apiName        上课打卡.
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/class/in?accesstoken=666
     * {
     *        "id":"30018"，             //上课ID
     *        "member_id":"48324"        //会员ID
     * }
     * @apiDescription   上课打卡
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/class/in?accesstoken=666
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
    public function actionIn($id, $member_id)
    {
        $today = date('Y-m-d');
        $clock = Clock::findOne(['employee_id'=>$this->coachId, 'date'=>$today]);
        //if(!$clock) return $this->error('请先打上班卡');
        $model = AboutClass::findOne($id);
        if(!$model) return $this->error('课不存在');
        if($model->member_id != $member_id) return $this->error('会员不符');
        if($model->coach_id != $this->coachId) return $this->error('课程教练不符');
        if($model->status != 1) return $this->error('课程状态不符');
        $models = AboutClass::find()->where(['member_id'=>$member_id])->asArray()->all();
        foreach ($models as $v){
            if ($v['status']==3){
                return $this->error('存在未下课，请先下课！');
            }
        }
        $model->status = 3;
        $return = $model->save() ? $this->success('','打卡成功') : $this->error('打卡失败', $model->errors);
        return $return;
    }

    /**
     * @api {get} /service/class/out?accesstoken=666   下课打卡
     * @apiVersion  1.0.0
     * @apiName        .下课打卡.
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/class/out?accesstoken=666
     * {
     *        "id":"30018"，             //上课ID
     *        "member_id":"48324"        //会员ID
     * }
     * @apiDescription   下课打卡
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/class/out?accesstoken=666
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
    public function actionOut($id, $member_id)
    {
        $model = AboutClass::findOne($id);
        if(!$model) return $this->error('课不存在');
        if($model->member_id != $member_id) return $this->error('会员不符');
        if($model->coach_id != $this->coachId) return $this->error('课程教练不符');
        if($model->status != 3) return $this->error('课程状态不符');
        $MemberCourseOrderDetails = MemberCourseOrderDetails::findOne($model['class_id']);
        $MemberCourseOder = MemberCourseOrder::findOne($MemberCourseOrderDetails['course_order_id']);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $model->status = 4;
            if (!$model->save()){
                \Yii::trace($model->errors);
                throw new \Exception('打卡失败！');
            }
            $MemberCourseOder->overage_section = $MemberCourseOder['overage_section'] - 1;
            if (!$MemberCourseOder->save()){
                \Yii::trace($model->errors);
                throw new \Exception('减课失败！');
            }
            if($transaction->commit())                                                               //事务提交
            {
                return $this->error('打卡失败！');
            }else{
                return $this->success('打卡成功！');
            }

        }catch (\Exception  $e) {
            $transaction->rollBack();                                                               //事务回滚
            return $e->getMessage();                                                               //捕捉错误，返回
        }
    }
    /**
     * 预约表(月)
     */
    /**
     * @api {get} /service/class/about-month-day?accesstoken=666   预约表(月)日
     * @apiVersion  1.0.0
     * @apiName        .预约表(月).日
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/class/about-month-day?accesstoken=666
     * {
     *       $time 2017-11-08
     * }
     * @apiDescription   .预约表(月点击出现日).
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net//class/about-month-day/class/about-month-day?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
    "message": "",
    "code": 1,
    "status": 200,
    -"data": {
    -"data": [
    -{
    "id": "13803",
    "status": 4,
    "name": "邵乐石",
    "start": "1510210500",
    "end": "1510214100"
    }
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
    public function actionAboutMonthDay()
    {
        $time = Yii::$app->request->get('time', 0);
        $data = [];
        $day=[];
        $abouts = AboutClass::find()->where(['coach_id'=> $this->coachId,'type'=>1])->andWhere(['status'=>[1,4]])->andWhere([ 'class_date'=>$time])->all();
            foreach ($abouts as $about){
                $day[] = [
                    'id' => $about->id,
                    'status' => $about->status,
                    'name' => Func::getRelationVal($about, 'memberDetails', 'name'),
                    'start' => date('H:i',$about->start),
                    'end' => date('H:i',$about->end),
                ];

            } 
            $data[] = $day;
            return $day;
    }
    /**
     * 预约表(周)
     */

    /**
     * @api {get} /service/class/about-week?accesstoken=666   预约表(周)
     * @apiVersion  1.0.0
     * @apiName        12预约表(周)
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/class/about-month?accesstoken=666
     * {
     *       "manage"，             //私教预约表（0）
     *        start  开始时间戳
     *        end 结束时间戳
     *
     * }
     * @apiDescription   .12预约表(周).
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/class/about-month?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    "message": "",
    "code": 1,
    "status": 200,
    "data":"data": [
    {
    "week": "五",
    "data": [
    {
    "id": "7039",
    "status": 4,
    "name": "邵乐石",
    "start": "1507876200",
    "end": "1507879800",
    "time": "14"
    },
    {
    "id": "8229",
    "status": 4, //1未上课 4已下课
    "name": "邵乐石",
    "start": "1508484600",
    "end": "1508488200",
    "time": "15"
    }
    ]
    },
    ]
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "打卡失败"    //失败原因
     *  }
     */
    public function actionAboutWeek($start, $end)
    {
        $start = Yii::$app->request->get('start', 0);
        $end = Yii::$app->request->get('end', 0);
        $data=[];
        $weekarray=array("日","一","二","三","四","五","六");
        for($time=$start; $time<=$end; $time+=3600*24){
            $abouts = AboutClass::find()->where(['coach_id'=> $this->coachId,'type'=>1,'status'=>[1,4],'class_date'=>date('Y-m-d',$time)])->all();
            unset($day);
            $day['week'] = $weekarray[date('w', $time)];
            foreach ($abouts as $about){
                $day['item'][]=[
                    'id' => $about->id,
                    'status' => $about->status,
                    'name' => Func::getRelationVal($about, 'memberDetails', 'name'),
                    'start' => $about->start,
                    'end' => $about->end,
                    'time'=>date('H',$about->create_at)
                ];
            }
            $data[] = $day;
        }
        return $data;
    }
    /**
     * @api {get} /service/class/about-day?accesstoken=666   预约表(日))
     * @apiVersion  1.0.0
     * @apiName        预约表(日))
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/class/about-month?accesstoken=666
     * {
     *       time= 2017-10-25
     *
     * }
     * @apiDescription   .预约表(日).
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/class/about-month?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    "message": "",
    "code": 1,
    "status": 200,
    "data":"data": {
    "item": [
    {
    "id": "49102",
    "status": "4",
    "name": "高冰洁", 姓名
    "start": "1508914620",  开始
    "end": "1508918220",    结束
    "num": 上课节数
    "course_amount": "0", //总节数
    "product_name": "0元WD减脂课程2A", 课程
    "money": "50.00" //课时费
    }
    ]
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionAboutDay($time){
        $abouts = AboutClass::find()
            ->select('ac.id,ac.status,(mco.money_amount/mco.course_amount) as unit_price,ac.class_id,mco.member_id,ac.status,md.name,ac.start,ac.end,mco.overage_section,mco.course_amount,mco.course_amount,mco.product_name,mcod.sale_price')
            ->alias('ac')
            ->joinWith(['memberCourseOrderDetails mcod'=>function($q){//会员购买私课订单详情表
                $q->joinWith('memberCourseOrder mco');//会员课程订单表
            }])->joinWith('memberDetails md')//会员详细信息表
            ->where(['ac.coach_id'=> $this->coachId,'ac.type'=>1])->andWhere(['ac.status'=>[1,4]])->andWhere(['ac.class_date'=>$time])->asArray()->all();
        $data=[];
        foreach ($abouts as $about){
            $data[] = [
                'id' => $about['id'],
                'status' => $about['status'],
                'name' => $about['name'],
                'start' => $about['start'],
                'end' => $about['end'],
                'num'=>$about['course_amount']-$about['overage_section'],//上课节数
                'course_amount'=>$about['course_amount'],//总节数
                'product_name'=>$about['product_name'],
                'money'=>sprintf("%.2f",$about['unit_price'])
            ];
        }
        return $data;

    }
    /**
     * 预约表(月)
     */
    /**
     * @api {get} /service/class/about-manage-month?accesstoken=666   管理预约表(月)
     * @apiVersion  1.0.0
     * @apiName        43管理预约表(月) 返回点 两个月公用一个的方法
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/class/about-manage-month?accesstoken=666
     * {
     *        year  年
     *        month    月
              manage 1管理 0 私教 管理预约表(月) 返回点 两个月公用一个的方法
     * }
     * @apiDescription   .管理预约表(月) 返回点 两个月公用一个的方法
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/class/about-manage-month?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    "message": "",
    "code": 1,
    "status": 200,
    "data": [
    {
    "date": "2017-09-01",
    "data": 1 //1是实心点 0为空
    }
    ]
    }
    ]
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "打卡失败"    //失败原因
     *  }
     */
    public function actionAboutManageMonth($year, $month)
    {
        $manage   = Yii::$app->request->get('manage', '0');
        $year   = Yii::$app->request->get('year', '0');
        $month   = Yii::$app->request->get('month', '0');
        if ($manage ==1){
            if(!$this->user->isManager) return $this->error('无私教经理权限');
            $id = Coach::find()->alias('e')->joinWith('organization o', FALSE)->where(['e.venue_id'=>$this->venueId, 'o.name'=>'私教部', 'e.status'=>1])->asArray()->all();
            foreach ($id as $i){
                $ids[]=$i['id'];
            };
        }else{
            $ids=$this->coachId;
        }
        if(empty($year) || empty($month)){
            $now = time();
            $year = date("Y",$now);
            $month =  date("m",$now);
        }
        $start = mktime(0,0,0,$month,1,$year);
        $end = mktime(23,59,59,($month+1),0,$year);
        $data = [];
//        for($time=$start; $time<=$end; $time+=3600*24){
//            $day['date'] = date('Y-m-d',$time);
//            $abouts = AboutClass::find()->where(['coach_id'=> $ids,'type'=>1])->andWhere(['status'=>[1,4],'class_date'=>$day['date']])->count();
//            if (empty($abouts)){
//                $day['data']=0;
//            }else{
//                $day['data']=1;
//            }
//            $data[] = $day;
//            unset($day);
//        }

        $abouts = AboutClass::find()->select('class_date ,count(id) as id')
            ->where(['coach_id'=> $ids,'type'=>1,'status'=>[1,4]])
            ->andWhere(['between', 'class_date', date('Y-m-d',$start),date('Y-m-d',$end)])
            ->groupBy('class_date')->all();
//        return $abouts ;
        for($time=$start; $time<=$end; $time+=3600*24){
            $a = false;
            foreach ($abouts as $K=>$V){
                if ($V['class_date'] == date('Y-m-d',$time)){
                    $a = true;
                }

            }
            if ($a){
                $day=['date'=>date('Y-m-d',$time),'data'=>1];
            }else{
                $day=['date'=>date('Y-m-d',$time),'data'=>0];
            }
            $data[]=$day;
            unset($day);
        }
        return $data;
    }
    /**
     * @api {get} /service/class/about-manage-week?accesstoken=666   管理预约表(周)
     * @apiVersion  1.0.0
     * @apiName        2管理预约表(周)
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/class/about-month?accesstoken=666
     * {
     *
     *        start  开始时间戳
     *         end 结束时间戳
     *
     * }
     * @apiDescription   .12管理预约表(周).
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/class/about-month?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
    "message": "",
    "code": 1,
    "status": 200,
    -"data": [
    -{
    "name": "唐成",
    -"data": [
    -{
    "week": "5",//星期 0-6
    "count": "2"//次数
    },
    -{
    "week": "6",
    "count": "3"
    },
    -{
    "week": "0",
    "count": "3"
    },
    },
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": "打卡失败"    //失败原因12
     *  }
     */
    public function actionAboutManageWeek($start, $end)
    {
        if(!$this->user->isManager) return $this->error('无私教经理权限');
//        $v = Admin::find()->alias('a')->where(['a.id'=>$this->userId])->joinWith('auth a')->all();
        $roleId =$this->user->level;
        $venuesId   =Auth::findOne(['role_id' => $roleId])->venue_id;
        $id = Coach::find()->alias('e')->joinWith('organization o', FALSE)->where(['e.venue_id'=>$venuesId, 'o.name'=>'私教部', 'e.status'=>1])->all();
        foreach ($id as $i){
            $ids[]=$i['id'];
        };
        for($time=$start; $time<=$end; $time+=3600*24){
            $week[]=date('Y-m-d',$time);
        }
//        for ($i=0;$i<count($ids);$i++){
//            $day['name']= $id[$i]['name'];
//            //优化数据结构 放在外面 查询以class——data分组
//
//            foreach ($week as $a){
//                $abouts = AboutClass::find()->where(['coach_id'=> $ids[$i],'type'=>1,'status'=>[1,4],'class_date'=>$a])->count();
//                $day['data'][]=[
//                    'week'=>date('w',strtotime($a)),
//                    'count' => $abouts
//                ];
//            }
//            $data[] =$day;
//            unset($day);
//        }
//        return $data;
        for ($i=0;$i<count($ids);$i++){
            $day['name']= $id[$i]['name'];
            //优化数据结构 放在外面 查询以class——data分组
            $abouts = AboutClass::find()->select('class_date ,count(id) as id')
                ->where(['coach_id'=> $ids[$i],'type'=>1,'status'=>[1,4]])
                ->andWhere(['between', 'class_date', date('Y-m-d',$start),date('Y-m-d',$end)])
                ->groupBy('class_date')->all();
//            return $abouts;
            for($j=0; $j<count($week);$j++){
                $a = false;
                foreach ($abouts as $K=>$V){
                    if ($V['class_date'] == date('Y-m-d',strtotime($week[$j]))){
                        $a = true;
                        $b= $V['id'];
                    }

                }
                if ($a){
                    $day['data'][]=['week'=>date('w',strtotime($week[$j])),'count'=>$b];
                    unset($b);
                }else{
                    $day['data'][]=['week'=>date('w',strtotime($week[$j])),'count'=>0];
                }
            }
            $data[]=$day;
            unset($day);
//            for ($j=0; $j<count($week);$j++){
//                if (isset($abouts[$j])&& $abouts[$j] ){
//                    $day['data'][]=[
//                            'count'=>$abouts[$j]['id'],
//                            'week'=>date('w',strtotime($week[$j]))
//                    ];
//                }else{
//                    $day['data'][]=[
//                        'count'=>'0',
//                        'week'=>date('w',strtotime($week[$j]))
//                    ];
//                }
//            }
//           $data[] =$day;
//           unset($day);
        }
        return $data;
    }

    /**
     * @api {get} /service/class/about-manage-day?accesstoken=666   管理预约表(日))
     * @apiVersion  1.0.0
     * @apiName        管理预约表(日))
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/class/about-month?accesstoken=666
     * {
     *       time= 2017-10-25
     *
     * }
     * @apiDescription   .预约表(日).
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/class/about-month?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
    "message": "",
    "code": 1,
    "status": 200,
    -"data": {
    -"item": [
    -{
    "name": "王淑红", //教练名称
    "status": 4,     //上课状态 1未上课 4已下课
    "sum": 10       //上课节数
    },
    -{
    "name": "冯倩",
    "status": 4,
    "sum": 8
    },
    -{
    "name": "高冰洁",
    "status": 4,
    "sum": 3
    },
    -{
    "name": "蔡萌",
    "status": 4,
    "sum": 2
    }
    ]
    }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionAboutManageDay($time)
    {
        if(!$this->user->isManager) return $this->error('无私教经理权限');
        
        $id = Coach::find()->alias('e')->joinWith('organization o', FALSE)->where(['e.venue_id'=>$this->venueId, 'o.name'=>'私教部', 'e.status'=>1])->asArray()->all();
        foreach ($id as $i){
            $ids[]=$i['id'];
        };
        $abouts = AboutClass::find()->alias('ac')
            ->where(['coach_id'=>$ids,'type'=>1])->joinWith('employee e')
            ->andWhere(['ac.status'=>[1,4]])
            ->andWhere(['ac.class_date'=>$time])->groupBy(['ac.coach_id','ac.status'])->orderBy('ac.status')->all();
        $array=[];
        foreach ($abouts as $value){
            $array[]=[
                'name'=> $value['employee']['name'],
                'coach_id'=>$value['coach_id'],
                'status'=>$value->status,
                'sum'=>count(AboutClass::find()->where(['coach_id'=>$value->coach_id,'status'=>$value->status,'class_date'=>$time])->Asarray()->all()),
            ];
        }
        return $array;

    }
    /**
     * @api {get} /service/class/cancel?accesstoken=666   12取消预约
     * @apiVersion  1.0.0
     * @apiName        12取消预约
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/class/acancel?accesstoken=666
     * {
     *       id  上课ID
     *
     * }
     * @apiDescription   12取消预约
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/03/08
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/class/cancel?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
    "code": 1,
    "status": "success",
    "message": "",
    "data": "取消预约成功！"
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
    "code": 0,
    "status": "error",
    "message": "异常"
    }
     */
    public function actionCancel(){
        $id  = Yii::$app->request->get('id',0);
        $data = AboutClass::findOne($id);
        if (empty($data)) return $this->error('不存在该记录!');
        if ($data->status == 1){
            $data->status = 2;//取消预约
            $data->is_read =0;
            $data->cancel_time = time();
            if ($data->save()){
                return $this->success('取消预约成功！');
            }
            return $this->error('取消预约失败！');
        }
        return $this->error('异常');
    }
	/**
	 * @api {post} /service/class/class-before-question?accesstoken=666   13课前询问答案提交
	 * @apiVersion  1.0.0
	 * @apiName        12课前询问答案提交
	 * @apiGroup       class
	 * @apiPermission 管理员
	 * @apiParamExample {json} 请求参数
	 *   post /service/class/acancel?accesstoken=666
	 * {
	 *       about_class_id  上课ID
	 *       member_id       会员id
	 *       answer_question 问题和答案的json
	 *
	 * }
	 * @apiDescription   12课前询问答案提交
	 * <br/>
	 * <span><strong>作    者：</strong></span>王亮亮<br/>
	 * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
	 * <span><strong>创建时间：</strong></span>2018/03/08
	 * @apiSampleRequest  http://apiqa.aixingfu.net/service/class/class-before-question?accesstoken=666
	 * @apiSuccess (返回值) {json} data
	 * @apiSuccessExample {json}返回值详情（成功）
	{
	"code": 1,
	"status": "success",
	"message": "",
	"data": "提交成功！"
	}
	 * @apiSuccessExample {json}返回值详情（失败）
	{
	"code": 0,
	"status": "error",
	"message": "提交失败"
	}
	 */
	public function actionClassBeforeQuestion(){
		$about_class_id = Yii::$app->request->post('about_class_id',0);
		$member_id = Yii::$app->request->post('member_id',0);
		$answer_question = Yii::$app->request->post('answer_question',0);
		if (empty($about_class_id) || empty($member_id) || empty($answer_question) ) return $this->error('参数异常!');
		$model = new MemberClassBeforeQuestion();
		$model->member_id = $member_id;
		$model->about_class_id = $about_class_id;
		$model->answer_question = json_encode($answer_question);
		if($model->save()){
			return $this->success('提交成功!');
		}else{
			return $this->error('提交失败!');
		}
	}




}
