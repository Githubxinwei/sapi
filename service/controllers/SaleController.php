<?php
namespace service\controllers;

use service\base\BaseController;
use service\models\AboutClass;
use common\models\Order;
use common\models\Func;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class SaleController extends BaseController
{
    /**
     * @api {get} /service/sale/count  销售统计
     * @apiName        1销售统计
     * @apiGroup       sale
     * @apiParam  {string}            type       日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParam  {string}            venue_id   场馆ID
     * @apiDescription   销售统计
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/08
     * @apiSampleRequest  http://apiqa.aixingfu.net/ 销售额统计/sale/count
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
            {
                "date": "2017-06-10",
                "sum": "1880.00"
            },
            {
                "date": "2017-06-17",
                "sum": "3460.00"
            },
            {
                "date": "2017-07-05",
                "sum": "1880.00"
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
        $params['venue_id'] =  Yii::$app->params['authVenueIds'];
        $venue_id = Yii::$app->request->get('venue_id', '0');
        if ($venue_id)  $params['venue_id'] = $venue_id;
        if(!in_array($type, ['d', 'w', 'm', 's', 'y'])) return $this->error('type错误');
        $start = strtotime(Func::getTokenClassDate($type, TRUE));
        $end = strtotime(Func::getTokenClassDate($type, FALSE));
        if($type == 'd'){
            $query = Order::find()->where(['venue_id'=>$params['venue_id'], 'status'=>2])->andWhere(['<>','note','回款'])->andWhere(['between', 'pay_money_time', $start, $end]);
            $orders = $query->all();
            $data = [];
            for($time = 3; $time<=24; $time+=3){
                $val['time'] = "$time:00";
                $val['sum'] = 0;
                foreach ($orders as $order){
                    $order_hour = date('G', $order->pay_money_time);
                    if($order_hour>=($time-3) && $order_hour<$time)
                        $val['sum']+=$order->total_price;
                }
                $val['sum'] = number_format($val['sum'], 2, '.', '');
                $data[] = $val;
            }
            return $data;
        }

        if(in_array($type, ['w', 'm'])){
            for($stageStart=$start; $stageStart<$end; $stageStart+=3600*24){
                $stageEnd = $stageStart+3600*24-1;
                $params['start'] = $stageStart;
                $params['end'] = $stageEnd;
                $sum = Order::saleSum($params);
                $data[]= [
                    'time' => date('Y-m-d', $stageStart),
                    'sum' => number_format($sum, 2, '.', ''),
                ];
            }
            return $data;
        }

        if($type == 's'){
            $data = [];
            $stageStart = $start;
            while($stageStart <= $end){
                $stageEnd = strtotime('+1 month', $stageStart)-1;
                $params['start'] = $stageStart;
                $params['end'] = $stageEnd;
                $sum = Order::saleSum($params);
                $data[]= [
                    'time' => date('Y-m', $stageStart),
                    'sum' => number_format($sum, 2, '.', ''),
                ];
                $stageStart = $stageEnd+1;
            };
            return $data;
        }

        if($type == 'y'){
            $data = [];
            $stageStart = $start;
            $stage = 0;
            while($stageStart <= $end){
                $stage++;
                $stageEnd = strtotime('+3 month', $stageStart)-1;
                $params['start'] = $stageStart;
                $params['end'] = $stageEnd;
                $sum = Order::saleSum($params);
                $data[]= [
                    'time' => (string)$stage,
                    'sum' => number_format($sum, 2, '.', ''),
                ];
                $stageStart = $stageEnd+1;
            };
            return $data;
        }

        return [];
    }

    /**
     * @api {get} /service/sale/table  销售报表
     * @apiName        2销售报表
     * @apiGroup       sale
     * @apiParam  {string}            start       开始日期(unix时间戳)
     * @apiParam  {string}            end         结束日期(unix时间戳)
     * @apiParam  {string}            behavior    商品类型(接口：/business/sale/behaviors)
     * @apiParam  {string}            type        日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParam  {string}            venue_id    场馆ID
     * @apiParam  {string}            page                  页码（可选，默认1）
     * @apiParam  {string}            per-page              每页显示数（可选，默认20）
     * @apiDescription   销售报表
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/08
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/sale/table
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "items": [
                {
                    "id": "478",
                    "order_number":"156431567832",//订单编号
                    "venue_name": "艾搏尊爵汇馆",
                    "member_name": "15537312038",//购买人
                    "note": "售卡",//商品类型
                    "product_name": "一年白金瑜伽卡",//商品名称
                    "total_price": "4099.00",//价格
                    "employee_name": "",
                    "single": "",
                    "pay_money_time": "1504084791"//时间
                },
                {
                    "id": "490",
                    "venue_name": "艾搏尊爵汇馆",
                    "member_name": "15537312038",
                    "note": "售卡",
                    "total_price": "4099.00",
                    "employee_name": "",
                    "single": "",
                    "pay_money_time": "1504163016"
                },
            ],
            "_links": {
                "self": {
                    "href": "http://127.0.0.3/business/sale/table?accesstoken=000_1544587932&type=y&page=1"
                },
                "next": {
                    "href": "http://127.0.0.3/business/sale/table?accesstoken=000_1544587932&type=y&page=2"
                },
                "last": {
                    "href": "http://127.0.0.3/business/sale/table?accesstoken=000_1544587932&type=y&page=61"
                }
            },
            "_meta": {
                "totalCount": 1215,
                "pageCount": 61,
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
        $behavior = Yii::$app->request->get('behavior', 0);
        $venue_id = $this->user->employee->venue_id;
        if(in_array($type, ['d', 'w', 'm', 's', 'y'], TRUE)){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
        }
        $query = \business\models\Order::find()->where(['venue_id'=>$venue_id, 'status'=>2]);

        if($start && $end) $query->andWhere(['between', 'pay_money_time', $start, $end]);
        if($behavior) $query->andWhere(['note'=>$behavior]);

        $query->orderBy('id desc');

        return new ActiveDataProvider(['query' => $query]);
    }

         /**
     * @api {get} /service/sale/behaviors  商品类型列表
     * @apiName        2商品类型列表
     * @apiGroup       sale
     * @apiDescription   商品类型列表
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/26
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/sale/behaviors
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
            "会员卡转卡",
            "办卡",
            "升级",
            "售卡",
            "购卡"
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
    public function actionBehaviors()
    {
        $data = Order::find()->select('note')->where(['venue_id'=>Yii::$app->params['authVenueIds']])->groupBy('note')->all();
        return ArrayHelper::getColumn($data,'note');
    }

     /**
     * @api {get} /service/sale/position  员工业绩统计
     * @apiName        3员工业绩统计
     * @apiGroup       sale
     * @apiParam  {string}            type       日期类别：d 日、w 周 、m 月 、s 季度 、y 年
      *@apiParam  {string}            venue_id   场馆ID
     * @apiDescription   员工业绩统计
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/08
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/sale/position
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
            {
                "position": null,
                "sum": "470401.00"
            },
             {
                "position": "私人教练",
                "sum": "579023.00"
            },
            {
                "position": "私教经理",
                "sum": "6160.00"
            },
            {
                "position": "销售员工",
                "sum": "72440.00"
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
    public function actionPosition()
    {
        $venue_id  = Yii::$app->request->get('venue_id', '0');
        if (!empty($venue_id)){
            $params['venue_id']=$venue_id;
        }else{
            $venue_id = $this->user->employee->venue_id;
        }
        $query = Order::find()->alias('o')->select('e.position, sum(o.total_price) as sum')
            ->joinWith('employeeS e')
            ->where(['o.venue_id'=>$venue_id, 'o.status'=>2, 'e.position'=>Yii::$app->params['countPositions']])
            ->andWhere(['<>', 'e.status', 2]);

        $type  = Yii::$app->request->get('type', 'd');
        if(in_array($type, ['d', 'w', 'm', 's', 'y'], TRUE)){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
            $query->andWhere(['between', 'o.pay_money_time', $start, $end]);
        }


        if($venue_id) $query->andWhere(['o.venue_id'=>$venue_id]);

        return $query->groupBy('e.position')->createCommand()->queryAll();
    }

    /**
     * @api {get} /service/sale/rank  销售排行榜
     * @apiName        4销售排行榜
     * @apiGroup       sale
     * @apiParam  {string}            type       日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParam  {string}            venue_id   场馆ID
     * @apiDescription   销售排行榜
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/08
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/sale/rank
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
            {
                "name": null,
                "sum": "229881.00"
            },
            {
                "name": "刘琳娜",
                "sum": "175299.00"
            },
            {
                "name": "张得恩",
                "sum": "13033.00"
            },
            {
                "name": "唐僧",
                "sum": "10719.00"
            },
            {
                "name": "邹学文",
                "sum": "6160.00"
            },
            {
                "name": "原文星",
                "sum": "2200.00"
            },
            {
                "name": "赵胜超",
                "sum": "1880.00"
            },
            {
                "name": "王聪",
                "sum": "1880.00"
            },
            {
                "name": "张丽",
                "sum": "1880.00"
            },
            {
                "name": "杨东洋",
                "sum": "1760.00"
            },
            {
                "name": "王耀",
                "sum": "1580.00"
            },
            {
                "name": "阎帅宏",
                "sum": "870.00"
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
        $venue_id  = Yii::$app->request->get('venue_id', '0');
        $params['venue_id'] = $this->user->employee->venue_id;
        if (!empty($venue_id))$params['venue_id']=$venue_id;
        $type  = Yii::$app->request->get('type', 'd');
        if(in_array($type, ['d', 'w', 'm', 's', 'y'], TRUE)){
            $params['start'] = strtotime(Func::getTokenClassDate($type, TRUE));
            $params['end'] = strtotime(Func::getTokenClassDate($type, FALSE));
        }
        return Order::saleRank($params);
    }

        /**
     * @api {get} /service/sale/about-count  课程预约统计
     * @apiName        5课程预约统计
     * @apiGroup       sale
     * @apiParam  {string}            type       日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParam  {string}            venue_id   场馆ID
     * @apiDescription   课程预约统计
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/22
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/sale/about-count
         * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
            {
                "name": "瑜伽",
                "count": "47"
            },
            {
                "name": "舞蹈",
                "count": "4"
            },
            {
                "name": "健身",
                "count": "5"
            },
            {
                "name": "其他",
                "count": "0"
            },
            {
                "name": "私课",
                "count": "0"
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
    public function actionAboutCount()
    {
        $venue_id  = Yii::$app->request->get('venue_id', '0');
        $params['company_id'] = $this->companyId;
        $params['venue_id'] = $this->user->employee->venue_id;
        if (!empty($venue_id))$params['venue_id']=$venue_id;
        $params['status']   = 1;
        //$params['class']    = 1;
        $type  = Yii::$app->request->get('type', 'd');
        if(in_array($type, ['d', 'w', 'm', 's', 'y'], TRUE)){
            $params['start'] = date('Y-m-d', strtotime(Func::getTokenClassDate($type, TRUE)));
            $params['end'] = date('Y-m-d', strtotime(Func::getTokenClassDate($type, FALSE)));
        }
        return AboutClass::classCount($params);
    }


}
