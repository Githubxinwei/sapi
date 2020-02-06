<?php
namespace service\controllers;

use service\models\AboutClass;
use service\models\CoachTeam; 
use service\models\MemberCourseOrderCoachPrivateCourse;
use common\models\Func;
use service\base\BaseController;
use Yii;
use business\models\MemberCourseOrder;
use business\models\MemberCourseDetail; 
use business\models\MemberBuyPrivateCourse; 
use yii\data\SqlDataProvider;
use yii\db\Expression;
use yii\data\ActiveDataProvider; 
use business\models\Member; 

class CoachController extends BaseController
{
    /**
     * @api {get} /service/coach/rank  卖课排行榜
     * @apiName        1卖课排行榜
     * @apiGroup       coach
     * @apiParam  {string}            type       日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParam  {string}            venue_id   场馆ID
     * @apiDescription   卖课排行榜
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/09
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/coach/rank 
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
                "coach_id": "1729", 
                "name": "冯灿林", 
                "member_num": "25", 
                "course_sum": "680", 
                "money_sum": "163127.00", 
                "deal_sum": "199" 
            },
            {
                "coach_id": "1731", 
                "name": "赵加衡", 
                "member_num": "2", 
                "course_sum": "22", 
                "money_sum": "6132.00", 
                "deal_sum": "3" 
            }
        ]
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "",
        "code": 0,
        "status": 422,
        "data": []
    }
     */
    public function actionRank()
    {
        $type  = Yii::$app->request->get('type');
        $venue_id = Yii::$app->request->get('venue_id', 0);
        $start_time = Yii::$app->request->get('start_time',0);
        $end_time = Yii::$app->request->get('end_time',0);
        // $start_time = 1498355534;
        // $end_time = 1519833600;
        $query = MemberCourseOrder::find() 
            ->select('mco.private_id as coach_id, e.name, e.pic, e.position as level, e.mobile, count(mco.member_id) as member_num, sum(course_amount) as course_sum, sum(money_amount) as money_sum') 
            ->andWhere(['mco.pay_status'=>1]) 
            //->andWhere(['or', ['mco.course_type'=>NULL], ['mco.course_type'=>1]])
            ->andWhere(['>', 'mco.money_amount', 0]);

        if($venue_id) $query->andWhere(['e.venue_id'=>$venue_id]);

        if(in_array($type, ['d', 'w', 'm', 's', 'y'])){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
            $query->andWhere(['between', 'o.order_time', $start, $end]);
        }else{
            $query->andWhere(['between', 'o.order_time', $start_time, $end_time]);
        }

        $listRs = $query->groupBy('mco.private_id')->orderBy('money_sum desc')->createCommand()->queryAll();

        $dealCourse = \common\models\MemberCourseOrder::find()
                        ->alias('mco')
                        ->joinWith('employeeS e')
                        ->select('mco.private_id, count(mco.member_id) as sum')
                        ->where(['e.venue_id' => Yii::$app->params['authVenueIds']])
                        ->andWhere(['mco.product_type' => 1, 'mco.pay_status' => 1])
                        ->andWhere(['or', ['mco.course_type' => null], ['mco.course_type' => 1]])
                        ->andWhere(['>', 'mco.money_amount', 0])
                        ->groupBy('mco.private_id')
                        ->asArray()
                        ->createCommand()
                        ->queryAll();
        foreach ($listRs as $k => $v) {
            foreach ($dealCourse as $v1) {
                if ($v['coach_id'] == $v1['private_id']) {
                    $listRs[$k]['deal_sum'] = $v1['sum'];
                }
            }

            !isset($listRs[$k]['deal_sum']) && $listRs[$k]['deal_sum'] = "0";
        }

        return $listRs;
    }

    /**
     * @api {get} /service/coach/count  私教上课量统计
     * @apiName        2私教上课量统计
     * @apiGroup       coach
     * @apiParam  {string}            type       日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParam  {string}            venue_id   场馆ID
     * @apiDescription   私教上课量统计
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/09
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/coach/count
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
            {
                "name": "胡媛媛",
                "num": "100"
            },
            {
                "name": "唐成",
                "num": "245"
            },
            {
                "name": "刘琳娜",
                "num": "329"
            },
            {
                "name": "王亚明",
                "num": "118"
            },
        ]
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "",
        "code": 0,
        "status": 422,
        "data": []
    }
     */
    public function actionCount()
    {
        $type = Yii::$app->request->get('type', 'd');
        $query = AboutClass::find()->select('e.name, count(*) as num')
            ->andWhere(['ac.status'=>4, 'ac.type'=>1]);

        if(in_array($type, ['d', 'w', 'm', 's', 'y'])){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
            $query->andWhere(['between', 'ac.start', $start, $end]);
        }

        $venue_id = Yii::$app->request->get('venue_id', 0);
        if($venue_id) $query->andWhere(['e.venue_id'=>$venue_id]);

        return $query->groupBy('ac.coach_id')->createCommand()->queryAll();
    }

    /**
     * @api {get} /service/coach/table  私教上课量报表
     * @apiName        3私教上课量报表
     * @apiGroup       coach
     * @apiParam  {string}            start       开始日期(unix时间戳)
     * @apiParam  {string}            end         结束日期(unix时间戳)
     * @apiParam  {string}            type        日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParam  {string}            venue_id    场馆ID
     * @apiParam  {string}            page                  页码（可选，默认1）
     * @apiParam  {string}            per-page              每页显示数（可选，默认20）
     * @apiDescription   私教上课量报表
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/09
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/coach/table
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "items": [
                {
                    "coach_name": "杨东洋",
                    "name": "张章云",
                    "mobile": "13803837888",
                    "num": "1",
                    "money": "300.00"
                },
                {
                    "coach_name": "李乾坤",
                    "name": "康伟",
                    "mobile": "18530010725",
                    "num": "1",
                    "money": "300.00"
                },
            ],
            "_links": {
                "self": {
                    "href": "http://127.0.0.3/business/coach/table?accesstoken=000_1544587932&type=y&page=1"
                },
                "next": {
                    "href": "http://127.0.0.3/business/coach/table?accesstoken=000_1544587932&type=y&page=2"
                },
                "last": {
                    "href": "http://127.0.0.3/business/coach/table?accesstoken=000_1544587932&type=y&page=14"
                }
            },
            "_meta": {
                "totalCount": "267",
                "pageCount": 14,
                "currentPage": 1,
                "perPage": 20
            }
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "",
        "code": 0,
        "status": 422,
        "data": []
    }
     */
    public function actionTable()
    {
        $start = Yii::$app->request->get('start', 0);
        $end   = Yii::$app->request->get('end', 0);
        $type  = Yii::$app->request->get('type', 0);
        $venue_id = Yii::$app->request->get('venue_id', 0);
        if(in_array($type, ['d', 'w', 'm', 's', 'y'], TRUE)){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
        }
        $dateField = new Expression('e.name as coach_name, md.name, m.mobile, count(*) as num, convert(sum(mco.money_amount/mco.course_amount),decimal(10,2)) as money');
        $query = AboutClass::find()->select($dateField)
            ->joinWith('member m')->joinWith('memberDetails md')
            ->joinWith([
                'memberCourseOrderDetails mod'=>function($query){
                    $query->joinWith(['memberCourseOrder mco'=>function($query){
                }], FALSE);
            }], FALSE)
            ->andWhere(['ac.status' => 4, 'ac.type'=>1, 'e.status' => 1]);

        if($venue_id) $query->andWhere(['e.venue_id'=>$venue_id]);
        if($start && $end) $query->andWhere(['between', 'ac.start', $start, $end]);

        $sql = $query->groupBy('ac.member_id, ac.coach_id')->orderBy('ac.id desc')->createCommand()->getRawSql();

        return new SqlDataProvider(['sql'=>$sql]);
    }

    /**
     * @api               {GET}           /service/coach/course-list                          卖课详情列表
     * @apiVersion        1.0.0
     * @apiName           卖课详情列表
     * @apiGroup          coach
     * @apiParam          {integer}          id          教练ID
     * @apiDescription    卖课详情列表
     * <br />
     * <span><strong>作    者：</strong></span>王海洋<br/>
     * <span><strong>邮    箱：</strong></span>wanghaiyang@xingfufit.com
     * <span><strong>创建时间：</strong></span>2018-04-13
     * @apiSampleRequest                  http://qaserviceapi.xingfufit.cn/service/coach/course-list
     * @apiSuccess        (返回值)           {json}                               data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code": 1,
        "data": [
            {
                "id": "56447",
                "pic": "http://oo0oj2qmr.bkt.clouddn.com/2281051522566488.JPG?e=1522570088&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:D72EPulUU5bf3QAYtNXuuiKOyRk=",
                "name": "李欣燃",
                "card": "0720002962",
                "course": "PT常规课",
                "fee": "256.38",
                "count": "6,666.00",
                "time": "2018-04-06",
                "status": 1
            },
            {
                "id": "53126",
                "pic": "http://oo0oj2qmr.bkt.clouddn.com/628181517042542.JPG?e=1517046142&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:mdw-kg1F4aN_71BeTdM-vtrG_3w=",
                "name": "罗琳浩",
                "card": "0720002866",
                "course": "PT常规课",
                "fee": "249.94",
                "count": "3,999.00",
                "time": "2018-01-26",
                "status": 1
            }
        ],
        "_links": {
            "self": {
                "href": "http://service.work/service/coach/course-list?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&id=1729&page=1"
            },
            "next": {
                "href": "http://service.work/service/coach/course-list?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&id=1729&page=2"
            },
            "last": {
                "href": "http://service.work/service/coach/course-list?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&id=1729&page=10"
            }
        },
        "_meta": {
            "totalCount": 199,
            "pageCount": 10,
            "currentPage": 1,
            "perPage": 20
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "code": 0,
        "status": "error",
        "message": "参数错误"
    }
     */
    public function actionCourseList()
    {
        $coach_id = Yii::$app->request->get('id', 0);
        if ($coach_id <= 0) {
            return $this->error('参数错误');
        }

        $query = MemberCourseDetail::find()
            ->alias('mco')
            ->joinWith('employeeS e')
            ->joinWith('member m')
            // ->joinWith('memberDetails md')
            ->joinWith('order o')
            ->joinWith('memberCardS mc')
            ->joinWith('memberCourseOrderDetailsOne mcod')
            // ->joinWith('course c', false)
            ->where(['e.venue_id'=>Yii::$app->params['authVenueIds']])
            ->andWhere(['mco.private_id' => $coach_id, 'mco.product_type' => 1])
            ->andWhere(['>', 'mco.money_amount', 0])
            ->andWhere(['or', ['mco.course_type' => null], ['mco.course_type' => 1]])
            ->orderBy('mco.id desc');

        return new ActiveDataProvider(['query' => $query]);
    }

    /**
     * @api               {GET}           /service/coach/index                          团队教练列表
     * @apiVersion        1.0.0
     * @apiName           团队教练列表
     * @apiGroup          coach
     * @apiDescription    团队教练列表
     * <br />
     * <span><strong>作    者：</strong></span>王海洋<br/>
     * <span><strong>邮    箱：</strong></span>wanghaiyang@xingfufit.com
     * <span><strong>创建时间：</strong></span>2018-04-13
     * @apiSampleRequest                  http://qaserviceapi.xingfufit.cn/service/coach/index
     * @apiSuccess        (返回值)           {json}                               data
     * @apiSuccessExample {json}返回值详情（成功）
     *

{
    "code": 1,
    "data": [
        {
            "id": "2167",
            "pic": "",
            "name": "翟刚庆",
            "level": "PT1",
            "mobile": "15003825657",
            "succ": 0,
            "count": 0
        },
        {
            "id": "1724",
            "pic": "http://oo0oj2qmr.bkt.clouddn.com/a3d0b655dad192f704b70cd77bf1ea265aca1b02a599d.png?e=1523198226&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:5_5-E8sNiz7dS6mZHfW7vBmxkME=",
            "name": "何亚辉",
            "level": "FM",
            "mobile": "15838003855",
            "succ": 0,
            "count": 0
        }
    ],
    "_links": {
        "self": {
            "href": "http://service.work/service/coach/index?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&page=1"
        },
        "next": {
            "href": "http://service.work/service/coach/index?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&page=2"
        },
        "last": {
            "href": "http://service.work/service/coach/index?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&page=2"
        }
    },
    "_meta": {
        "totalCount": 21,
        "pageCount": 2,
        "currentPage": 1,
        "perPage": 20
    }
}
     * @apiSuccessExample {json}返回值详情（失败）
{
    "code": 0,
    "status": "error",
    "message": "未知错误"
}
     */
    public function actionIndex()
    {
        $query = CoachTeam::find()
            ->alias('e')
            ->joinWith('organization o', FALSE)
            // ->where(['e.venue_id'=>$this->venueId, 'o.name'=>'私教部', 'e.status'=>1])
            ->where(['e.venue_id'=>$this->venueId, 'o.name'=>'私教部'])
            ->orderBy('id desc');
 
        $provider = new ActiveDataProvider(['query' => $query]);
        return $provider;
    }

    /**
     * @api               {GET}           /service/coach/detail                          教练详情
     * @apiVersion        1.0.0
     * @apiName           教练详情
     * @apiGroup          coach
     * @apiParam          {integer}          id          教练ID
     * @apiDescription    教练详情
     * <br />
     * <span><strong>作    者：</strong></span>王海洋<br/>
     * <span><strong>邮    箱：</strong></span>wanghaiyang@xingfufit.com
     * <span><strong>创建时间：</strong></span>2018-04-13
     * @apiSampleRequest                  http://qaserviceapi.xingfufit.cn/service/coach/detail
     * @apiSuccess        (返回值)           {json}                               data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code": 1,
        "status": "success",
        "message": "",
        "data": {
            "name": "吴浩哲",
            "pic": "http://oo0oj2qmr.bkt.clouddn.com/5497121521166917.jpg?e=1521170517&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:M9_Unq31Qt-6GkA0VZ7IGj2ODS4=",
            "sex": "男",
            "level": "PT2",
            "mobile": "13137139101",
            "company": "我爱运动瑜伽健身",
            "venue": "帝湖瑜伽健身馆",
            "status": "在职"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "code": 0,
        "status": "error",
        "message": "信息错误"
    }
     */
    public function actionDetail()
    {
        $coach_id = Yii::$app->request->get('id', 0);
        if ($coach_id <= 0) {
            return $this->error('参数错误');
        }

        $infoRs = CoachTeam::find()
            ->alias('e')
            ->joinWith('organization o', FALSE)
            ->joinWith('venue v', FALSE)
            ->joinWith('company c', FALSE)
            ->select('e.name, e.pic, e.sex, e.position as level, e.mobile, c.name as company, v.name as venue, e.status')
            ->where(['e.id' => $coach_id, 'e.venue_id'=>$this->venueId, 'o.name'=>'私教部'])
            ->asArray()
            ->one();
        if (!isset($infoRs['name'])) {
            return $this->error('信息错误');
        }
        $infoRs['sex'] == 1 ? $infoRs['sex'] = '男' : ($infoRs['sex'] == 2 ? $infoRs['sex'] = '女' : $infoRs['sex'] = '未知');
        $infoRs['status'] == 1 ? $infoRs['status'] = '在职' : ($infoRs['status'] == 2 ? $infoRs['status'] = '离职' : $infoRs['status'] = '未知');
        return $this->success($infoRs);
    }

    /**
     * @api               {GET}           /service/coach/private-course                          购买收费私课会员列表
     * @apiVersion        1.0.0
     * @apiName           购买收费私课会员列表
     * @apiGroup          coach
     * @apiParam          {integer}          id          教练ID
     * @apiParam          {type}             status      状态  1已购[默认]、0未购
     * @apiDescription    购买收费私课会员列表
     * <br />
     * <span><strong>作    者：</strong></span>王海洋<br/>
     * <span><strong>邮    箱：</strong></span>wanghaiyang@xingfufit.com
     * <span><strong>创建时间：</strong></span>2018-04-13
     * @apiSampleRequest                  http://qaserviceapi.xingfufit.cn/service/coach/private-course
     * @apiSuccess        (返回值)           {json}                               data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code": 1,
        "data": [
            {
                "id": "95835",
                "name": "周昊",
                "sex": "男",
                "mobile": "13526672942",
                "time": "2018-02-04"
            },
            {
                "id": "95957",
                "name": "刘聪",
                "sex": "男",
                "mobile": "13213018532",
                "time": "2018-02-06"
            }
        ],
        "_links": {
            "self": {
                "href": "http://service.work/service/coach/private-course?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&id=1730&status=0&page=1"
            }
        },
        "_meta": {
            "totalCount": 4,
            "pageCount": 1,
            "currentPage": 1,
            "perPage": 20
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "code": 0,
        "status": "error",
        "message": "参数错误"
    }
     */
    public function actionPrivateCourse()
    {
        $coach_id = Yii::$app->request->get('id', 0);
        $status = Yii::$app->request->get('status', 1);
        if ($coach_id <= 0 || !in_array($status, [0, 1])) {
            return $this->error('参数错误');
        }

        if ($status != 1) {
            $dealMemberIds = MemberCourseOrderCoachPrivateCourse::find()
                ->select('member_id')
                ->where(['private_id' => $coach_id])
                // ->andWhere(['mco.product_type' => 1, 'mco.pay_status' => 1])
                ->andWhere(['or', ['course_type' => null], ['course_type' => 1]])
                ->andWhere(['>', 'money_amount', 0])
                ->groupBy('member_id')
                ->asArray()
                ->all();
            $dealMemberIds = array_column($dealMemberIds, 'member_id');
        }

        $query = MemberCourseOrderCoachPrivateCourse::find()
            ->alias('mco')
            ->joinWith('member m', false)
            ->joinWith('memberDetails md', false)
            ->where(['mco.private_id' => $coach_id])
            ->orderBy('m.register_time desc')
            ->groupBy('m.id');
        if ($status == 1) {
            // $query->andWhere(['mco.product_type' => 1]);
            $query->andWhere(['or', ['mco.course_type' => null], ['mco.course_type' => 1]]);
            $query->andWhere(['>', 'mco.money_amount', 0]);
            // $query->andWhere(['mco.product_type' => 1, 'mco.pay_status' => 1]);
        } else {
            // $query->andWhere(['is', 'mco.product_type', null]);
            $query->andWhere(['not in', 'm.id', $dealMemberIds]);
        }

        return new ActiveDataProvider(['query' => $query]);
    }
}
