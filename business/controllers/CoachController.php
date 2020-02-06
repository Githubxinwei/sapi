<?php
namespace business\controllers;

use business\models\AboutClass;
use common\models\Func;
use Yii;
use business\models\MemberCourseOrder;
use yii\data\SqlDataProvider;
use yii\db\Expression;

class CoachController extends BaseController
{
    /**
     * @api {get} /business/coach/rank  卖课排行榜
     * @apiName        1卖课排行榜
     * @apiGroup       coach
     * @apiParam  {string}            type       日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParam  {string}            venue_id   场馆ID
     * @apiDescription   卖课排行榜
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/09
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/coach/rank
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
            {
                "name": "刘琳娜",
                "member_num": "152",
                "course_sum": "951",
                "money_sum": "180819.00"
            },
            {
                "name": "赵国权",
                "member_num": "194",
                "course_sum": "789",
                "money_sum": "138974.00"
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
    public function actionRank()
    {
        $type  = Yii::$app->request->get('type', 0);
        $venue_id = Yii::$app->request->get('venue_id', 0);
        $query = MemberCourseOrder::find()->select('e.name, count(mco.member_id) as member_num, sum(course_amount) as course_sum, sum(money_amount) as money_sum')
            ->andWhere(['mco.pay_status'=>1])
            //->andWhere(['or', ['mco.course_type'=>NULL], ['mco.course_type'=>1]])
            ->andWhere(['>', 'mco.money_amount', 0]);

        if($venue_id) $query->andWhere(['e.venue_id'=>$venue_id]);

        if(in_array($type, ['d', 'w', 'm', 's', 'y'])){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
            $query->andWhere(['between', 'o.order_time', $start, $end]);
        }

        return $query->groupBy('mco.private_id')->orderBy('money_sum desc')->createCommand()->queryAll();
    }

    /**
     * @api {get} /business/coach/count  私教上课量统计
     * @apiName        2私教上课量统计
     * @apiGroup       coach
     * @apiParam  {string}            type       日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParam  {string}            venue_id   场馆ID
     * @apiDescription   私教上课量统计
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/09
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/coach/count
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
     * @api {get} /business/coach/table  私教上课量报表
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
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/09
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/coach/table
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


}
