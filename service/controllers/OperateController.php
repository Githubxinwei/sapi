<?php
namespace service\controllers;

use service\models\EntryRecord;
use common\models\AboutClass;
use common\models\MemberCard;
use service\base\BaseController;
use Yii;
use yii\data\ActiveDataProvider;
use common\libs\SqlDataProvider;
use common\models\Func;

class OperateController extends BaseController
{

    /**
     * @api {get} /service/operate/entry-count  到店人数统计
     * @apiName        1到店人数统计
     * @apiGroup       operate
     * @apiParam  {string}            date       可选，默认当日，"2017-12-03"
     * @apiParam  {string}            venue_id   场馆ID
     * @apiParam  {string}           member_type   潜在会员  获取传2 被带会员  获取传1
     * @apiDescription   到店人数统计
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/03
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/operate/entry-count
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "0:00": 0,
            "1:00": 0,
            "2:00": 0,
            "3:00": 0,
            "4:00": 0,
            "5:00": 0,
            "6:00": 0,
            "7:00": 0,
            "8:00": 0,
            "9:00": 17,
            "10:00": 9,
            "11:00": 4,
            "12:00": 7,
            "13:00": 3,
            "14:00": 18,
            "15:00": 29,
            "16:00": 32,
            "17:00": 34,
            "18:00": 40,
            "19:00": 31,
            "20:00": 7,
            "21:00": 0,
            "22:00": 0,
            "23:00": 0
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
    public function actionEntryCount()
    {
        ini_set('max_execution_time', '0');
        $date   = Yii::$app->request->get('type', 'd');
        if(in_array($date, ['d', 'w', 'm', 's', 'y'])){
            $start = strtotime(Func::getTokenClassDate($date, TRUE));
            $end = strtotime(Func::getTokenClassDate($date, FALSE));
        }
        $query  = EntryRecord::find()->alias('er')->where(['er.venue_id' => Yii::$app->params['authVenueIds']])->andWhere(['between', 'er.entry_time', $start, $end]);

        $venue_id = Yii::$app->request->get('venue_id', 0);
        if($venue_id) $query->andWhere(['er.venue_id'=>$venue_id]);
        $member_type =Yii::$app->request->get('member_type',0);
        if($member_type==2){
            $query->joinWith('member me')->andWhere(['me.member_type'=>$member_type]);
        }elseif ($member_type==1){
            $query->joinWith('memberCard mc')->andWhere(['<>','mc.pid','null']);
        }
        $entrys = $query->all();

        //无数据也产生一条以便于产生返回时间数据
        if(empty($entrys)){
            $entrys[] = new EntryRecord();
            $entrys[0]->entry_time = 0;
        }

        $data = $return = [];
        foreach ($entrys as $entry){
            for($startTime=$start; $startTime<$end; $startTime+=3600){
                $endTime = $startTime+3600;
                $point = date('G:i', $startTime);
                if(!isset($data[$point])) $data[$point] = 0;
                if($entry->entry_time >= $startTime && $entry->entry_time < $endTime) $data[$point]++;
            }
        }
        foreach ($data as $time=>$count) $return[] = compact('time','count');
        return $return;
    }

    /**
     * @api {get} /service/operate/entry-table  到店统计报表
     * @apiName        2到店统计报表
     * @apiGroup       operate
     * @apiParam  {string}            date       可选，默认当日，"2017-12-03"
     * @apiParam  {int}               venue_id   可选，场馆ID
     * @apiParam  {string}            page                  页码（可选，默认1）
     * @apiParam  {string}            per-page              每页显示数（可选，默认20）
     * @apiDescription   到店统计报表
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/03
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/operate/entry-table
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "items": [
                {
                    "id": "80668",
                    "entry_time": "1512522313",
                    "name": "任海音",
                    "card_number": "20700017",
                    "mobile": "13598827688",
                    "employee": "王亚明",
                    "coach": "王亚明"
                },
                {
                    "id": "80670",
                    "entry_time": "1512522419",
                    "name": "黄凤枝",
                    "card_number": "61000253",
                    "mobile": "13673386836",
                    "employee": "赵亚萍",
                    "coach": "赵亚萍"
                },
            ],
            "_links": {
                "self": {
                    "href": "http://127.0.0.3/service/operate/entry-table?accesstoken=000_1544587932&date=2017-12-06&page=1"
                },
                "next": {
                    "href": "http://127.0.0.3/service/operate/entry-table?accesstoken=000_1544587932&date=2017-12-06&page=2"
                },
                "last": {
                    "href": "http://127.0.0.3/service/operate/entry-table?accesstoken=000_1544587932&date=2017-12-06&page=12"
                }
            },
            "_meta": {
                "totalCount": 231,
                "pageCount": 12,
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
    public function actionEntryTable()
    {
        $venue_id = Yii::$app->request->get('venue_id', 0);
        $date     = Yii::$app->request->get('date', 0) ?: date('Y-m-d');
        $start    = strtotime($date);
        $end      = strtotime($date . ' 23:59:59');
        $query    = EntryRecord::find()->where(['venue_id' => Yii::$app->params['authVenueIds']])->andWhere(['between', 'entry_time', $start, $end]);
        if($venue_id) $query->andWhere(['venue_id'=>$venue_id]);
        return new ActiveDataProvider(['query'=>$query]);
    }

    /**
     * @api {get} /service/operate/category-count  到店卡种统计
     * @apiName        3到店卡种统计
     * @apiGroup       operate
     * @apiParam  {string}            start       开始日期(unix时间戳)
     * @apiParam  {string}            end         结束日期(unix时间戳)
     * @apiParam  {string}            venue_id   场馆ID
     * @apiDescription   到店卡种统计
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/03
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/operate/category-count
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "items": [
                {
                    "card_name": "次卡",
                    "num": "1"
                },
                {
                    "card_name": "游泳长训班一周一次6个月",
                    "num": "2"
                },
                {
                    "card_name": "游泳长训班一周两次一年",
                    "num": "1"
                },
                {
                    "card_name": "游泳长训班一周一次一年",
                    "num": "1"
                },
            ],
            "_links": {
                "self": {
                    "href": "http://127.0.0.3/service/operate/category-count?accesstoken=000_1544587932&page=1"
                },
                "next": {
                    "href": "http://127.0.0.3/service/operate/category-count?accesstoken=000_1544587932&page=2"
                },
                "last": {
                    "href": "http://127.0.0.3/service/operate/category-count?accesstoken=000_1544587932&page=6"
                }
            },
            "_meta": {
                "totalCount": "116",
                "pageCount": 6,
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
    public function actionCategoryCount()
    {
        $venue_id = $this->getFilterVenueId();
        $query = EntryRecord::find()->alias('er')->select('cc.card_name, count(er.id) as num')
            ->joinWith(['memberCard mc'=>function($q){
                $q->joinWith('cardCategory cc');
            }])
            ->andWhere(['er.venue_id'=>$venue_id]);

        $type  = Yii::$app->request->get('type', 'd');
        if(in_array($type, ['d', 'w', 'm', 's', 'y'], TRUE)){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
            $query->andWhere(['between', 'er.entry_time', $start, $end]);
        }

        $queryCount = clone $query;

        $sql = $query->groupBy('cc.id')->orderBy('count(er.id) desc')->createCommand()->getRawSql();
        return new SqlDataProvider([
            'sql'=>$sql,
            'pagination' => [
                'pageSizeLimit' => [0],
            ],
            'extra'=>['count'=>$queryCount->count()]
        ]);
    }


    /**
     * @api {get} /service/operate/class-count  会员上课统计
     * @apiName        4会员上课统计
     * @apiGroup       operate
     * @apiParam  {string}            start       可选，默认当日，"2017-12-03"
     * @apiParam  {string}            end         可选，默认当日，"2017-12-03"
     * @apiParam  {string}            venue_id    场馆ID
     * @apiDescription   会员上课统计
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/03
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/operate/class-count
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "瑜伽": "320",
            "舞蹈": "35",
            "健身": "68",
            "其他": "0",
            "私课": "1036"
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
    public function actionClassCount()
    {
        $params['company_id'] = $this->companyId;
        $params['start']      = Yii::$app->request->get('start', 0) ?: date('Y-m-d');
        $params['end']        = Yii::$app->request->get('end', 0) ?: date('Y-m-d');
        $params['venue_id']   = Yii::$app->params['authVenueIds'];
        $params['status']     = [3, 4];
        //$params['class']    = 1;

        $venue_id = Yii::$app->request->get('venue_id', 0);
        if($venue_id) $params['venue_id'] = $venue_id;

        return AboutClass::classCount($params);
    }

}
