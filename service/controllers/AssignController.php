<?php
namespace service\controllers;

use service\models\Assign;
use service\models\AssignList;
use service\models\AssignRecord;
use service\models\AssignHistory;
use service\base\BaseController;
use service\base\ActiveDataProvider;
use yii\rest\Serializer;
use Yii;
use yii\data\Pagination;

class AssignController extends BaseController
{
    /**
     * @api {GET} /service/assign/index 教练分配会员列表
     * @apiVersion  1.0.0
     * @apiName           教练分配会员列表
     * @apiGroup          assign
     * @apiParam          {string}          from_date         开始时间
     * @apiDescription    教练分配会员列表
     * <br />
     * <span><strong>作    者：</strong></span>王海洋<br/>
     * <span><strong>邮    箱：</strong></span>wanghaiyang@xingfufit.com
     * <span><strong>创建时间：</strong></span>2018-04-13
     * @apiSampleRequest       http://qaserviceapi.xingfufit.cn/service/assign/index
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code": 1,
        "data": [
            {
                "date": "05月",
                "count": 1,
                "list": [
                    {
                        "id": "52",
                        "pic": null,
                        "name": "杨慧磊",
                        "mobile": "18739908474",
                        "member_id": "100688",
                        "coach_name": "陈钱",
                        "assign_name": "刘佳佳",
                        "assign_time": "05-02 19:49"
                    }
                ]
            },
            {
                "date": "12月",
                "count": 2,
                "list": [
                    {
                        "id": "53",
                        "pic": null,
                        "name": "王琪",
                        "mobile": "13683806248",
                        "member_id": "96063",
                        "coach_name": "陈钱",
                        "assign_name": "刘佳佳",
                        "assign_time": "12-14 22:29"
                    },
                    {
                        "id": "44",
                        "pic": null,
                        "name": "王琪",
                        "mobile": "13683806248",
                        "member_id": "96063",
                        "coach_name": "陈钱",
                        "assign_name": "刘佳佳",
                        "assign_time": "12-14 22:26"
                    }
                ]
            }
        ],
        "_links": {
            "self": {
                "href": "http://service.work/service/assign/index?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&page=1"
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
        "name": "Unauthorized",
        "message": "Your request was made with invalid credentials.",
        "code": 0,
        "status": 401,
        "type": "yii\\web\\UnauthorizedHttpException"
    }
     */
    public function actionIndex()
    {
        $from_date = Yii::$app->request->get('from_date', '');

        if ($from_date != '' && strtotime($from_date) === false) {
            $from_date = 0;
        } elseif ($from_date != '') {
            $from_date = strtotime($from_date);
        } else {
            $from_date = 0;
        }
        
        $query = Assign::find()
            ->select("id,coach_id")
            ->groupBy(["DATE_FORMAT(FROM_UNIXTIME(create_at), '%Y-%m')"])
            ->orderBy('id desc');
        $query = $this->groupMonthData($query, $from_date);
        $query->createCommand()->getRawSql();
        $query = new ActiveDataProvider(['query' => $query]);

        return $query;
    }

    private function groupMonthData($query, $from_date)
    {
        $query->where(['create_id' => $this->employeeId]);
        // $query->where(['create_id' => 181]);
        if ($from_date > 0) {
            $query->andWhere(['>=', 'create_at', $from_date]);
            $end_date = strtotime('+1 months', $from_date);
            $query->andWhere(['<', 'create_at', $end_date]);
        }
        return $query;
    }

    /**
     * @api {GET} /service/assign/lists 消息中心会员分配列表
     * @apiVersion  1.0.0
     * @apiName           消息中心会员分配列表
     * @apiGroup          assign
     * @apiParam          {integer}         coach_id          教练ID
     * @apiDescription    消息中心会员分配列表
     * <br />
     * <span><strong>作    者：</strong></span>王海洋<br/>
     * <span><strong>邮    箱：</strong></span>wanghaiyang@xingfufit.com
     * <span><strong>创建时间：</strong></span>2018-04-13
     * @apiSampleRequest                  http://qaserviceapi.xingfufit.cn/service/assign/lists
     * @apiSuccess        (返回值)           {json}                               data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code": 1,
        "data": [
            {
                "id": "67",
                "name": "杨慧磊",
                "mobile": "18739908474",
                "member_id": "100688",
                "assign_name": "刘佳佳",
                "assign_time": "04-09 16:32:50",
                "status": 1
            },
            {
                "id": "48",
                "name": "娄永霞",
                "mobile": "13937159957",
                "member_id": "95906",
                "assign_name": "刘佳佳",
                "assign_time": "04-09 16:12:47",
                "status": 0
            }
        ],
        "_links": {
            "self": {
                "href": "http://service.work/service/assign/lists?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&page=1"
            },
            "next": {
                "href": "http://service.work/service/assign/lists?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&page=2"
            },
            "last": {
                "href": "http://service.work/service/assign/lists?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&page=4"
            }
        },
        "_meta": {
            "totalCount": 67,
            "pageCount": 4,
            "currentPage": 1,
            "perPage": 20
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "name": "Unauthorized",
        "message": "Your request was made with invalid credentials.",
        "code": 0,
        "status": 401,
        "type": "yii\\web\\UnauthorizedHttpException"
    }
     */
    public function actionLists()
    {
        $coach_id = Yii::$app->request->get('coach_id', 0);
        $coach_id == 0 && $coach_id = $this->coachId;

        $query = AssignList::find()->alias('a')
            ->joinWith('member m', FALSE)
            ->joinWith('memberDetails md', FALSE)
            ->joinWith('memberCard mc', FALSE)
            ->joinWith('employee e', FALSE)
            ->joinWith('management ma', FALSE)
            ->where(['a.coach_id' => $coach_id])
            ->orderBy('id desc');
        $query = new ActiveDataProvider(['query' => $query]);

        return $query;

    }

    /**
     * @api               {GET}           /service/assign/record                          分配详情
     * @apiVersion  1.0.0
     * @apiName           分配详情
     * @apiGroup          assign
     * @apiParam          {integer}          id          分配记录ID
     * @apiDescription    分配详情
     * <br />
     * <span><strong>作    者：</strong></span>王海洋<br/>
     * <span><strong>邮    箱：</strong></span>wanghaiyang@xingfufit.com
     * <span><strong>创建时间：</strong></span>2018-04-13
     * @apiSampleRequest                  http://qaserviceapi.xingfufit.cn/service/assign/record
     * @apiSuccess        (返回值)           {json}                               data
     * @apiSuccessExample {json}返回值详情（成功）
     *
    {
        "code": 1,
        "status": "success",
        "message": "",
        "data": {
            "id": "65",
            "pic": "http://oo0oj2qmr.bkt.clouddn.com/8660551517982835.jpg?e=1517986435&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:A686VNygyZYUJuXTfA-iNXpfoHA=",
            "name": "刘聪",
            "sex": "男",
            "mobile": "13213018532",
            "member_id": "95957",
            "assign_name": "刘佳佳",
            "assign_time": "2018-04-09 16:32:50",
            "list": [
                {
                    "id": "65",
                    "name": "陈钱",
                    "create_at": "2018.04.09"
                },
                {
                    "id": "18",
                    "name": "陈钱",
                    "create_at": "2018.04.09"
                }
            ]
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
{
    "code": 0,
    "status": "error",
    "message": "数据错误"
}
     */
    public function actionRecord()
    {
        $id = Yii::$app->request->get('id', 0);

        if ($id <= 0) {
            return $this->error('参数错误');
        }

        $query = AssignRecord::find()->alias('a')
            ->joinWith('member m', FALSE)
            ->joinWith('memberDetails md', FALSE)
            ->joinWith('management ma1', FALSE)
            ->where(['a.id' => $id]);
        $return = new ActiveDataProvider(['query' => $query]);
        $serialize = new Serializer();
        $return = $serialize->serialize($return);
        if (!isset($return[0]['id'])) {
            return $this->error('数据错误');
        }
        $return = $return[0];
        if (!isset($return['member_id']) || $return['member_id'] <= 0) {
            $return['list'] = array();
        } else {
            $listRs = AssignRecord::find()->alias('a')
            ->joinWith('employee e', FALSE)
            ->where(['a.member_id' => $return['member_id']])
            ->select('a.id, e.name, a.create_at')
            ->orderBy('id desc')
            ->limit(10)
            ->asArray()
            ->all();
            foreach ($listRs as $k => $v) {
                $listRs[$k]['create_at'] = date('Y.m.d', $v['create_at']);
            }
            $return['list']= $listRs;
        }

        $assign = AssignRecord::findOne($id);
        $assign->is_read = 1;
        $assign->save();
        return $this->success($return);
    }
}
