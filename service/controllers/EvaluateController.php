<?php
namespace service\controllers;

use common\models\base\Course;
use common\models\MemberDetails;
use service\models\Coach;
use service\models\EvaluateList;
use service\models\EvaluateView;
use service\models\Member;
use service\base\BaseController;
use service\base\ActiveDataProvider;
use Yii;

class EvaluateController extends BaseController
{
    /**
     * @api               {GET}           /service/evaluate/lists            会员评价列表
     * @apiVersion        1.0.0
     * @apiName           会员评价列表
     * @apiGroup          evaluate
     * @apiDescription    会员评价列表
     * <br />
     * <span><strong>作    者：</strong></span>王海洋<br/>
     * <span><strong>邮    箱：</strong></span>wanghaiyang@xingfufit.com
     * <span><strong>创建时间：</strong></span>2018-04-17
     * @apiSampleRequest                  http://qaserviceapi.xingfufit.cn/service/evaluate/lists
     * @apiSuccess        (返回值)           {json}                                      data
     * @apiSuccessExample {json}返回值详情（成功）
     *
{
    "code": 1,
    "data": [
        {
            "id": "65",
            "pic": "http://oo0oj2qmr.bkt.clouddn.com/哈他精准.jpg?e=1494054698&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:ma6HmJ9PBEIDh5SmG39tuOuQT98=",
            "name": "哈他瑜伽",
            "type": "团教课",
            "level": 5,
            "content": null,
            "time": "2018-04-08 16:43"
        },
        {
            "id": "5",
            "pic": "http://oo0oj2qmr.bkt.clouddn.com/身体平衡.jpg?e=1494054520&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:xHvoyC4Pw61SFvY3_RG5fIhct50=",
            "name": "身体平衡",
            "type": "团教课",
            "level": 4,
            "content": null,
            "time": "2018-04-02 10:17"
        }
    ],
    "_links": {
        "self": {
            "href": "http://service.work/service/evaluate/lists?accesstoken=sPp53Aj0SCFesA4Y1Opz37gBVtM6dQvd_1523797456&page=1"
        }
    },
    "_meta": {
        "totalCount": 17,
        "pageCount": 1,
        "currentPage": 1,
        "perPage": 20
    }
}
     * @apiSuccessExample {json}返回值详情（失败）
{
    "code": 0,
    "status": "error",
    "message": "没有私教"
}
     */
    public function actionLists()
    {
        $start = Yii::$app->request->get('start', 0);
        $end = Yii::$app->request->get('end', 0);
        $coachRs = Coach::find()
            ->alias('e')
            ->joinWith('organization o', false)
            ->select('e.id')
            // ->where(['e.venue_id' => $this->venueId, 'o.name' => '私教部', 'e.status' => 1])
            ->where(['e.venue_id' => $this->venueId, 'e.status' => 1])
            ->orderBy('e.id asc')
            ->asArray()
            ->all();
        if (count($coachRs) == 0) {
            return $this->error('没有私教');
        }
        $coachRs = array_column($coachRs, 'id');

        // $memberRs = Member::find()
        //     ->select('id')
        //     ->where(['private_id' => $coachRs])
        //     // ->andWhere(['status' => 1])
        //     ->orderBy('id asc')
        //     ->asArray()
        //     ->all();
        // if (count($memberRs) == 0) {
        //     return $this->error('没有会员');
        // }
        // $memberRs = array_column($memberRs, 'id');

        $query = EvaluateList::find()
            ->alias('e')
            ->joinWith('groupClass gc', false)
            ->where(['in', 'gc.coach_id', $coachRs])
            ->orderBy('id desc');
        if ($start > 0) {
            $query->andWhere(['>=', 'create_at', $start]);
        }
        if ($end > 0) {
            $query->andWhere(['<=', 'create_at', $end]);
        }
        $query = new ActiveDataProvider(['query' => $query]);

        return $query;
    }
    /**
     * @api               {GET}           /service/evaluate/detail            会员评价详情
     * @apiVersion        1.0.0
     * @apiName           1会员评价详情
     * @apiGroup          evaluate
     * @apiDescription    1会员评价详情
     * <br />
     * @apiParam  {string}            id               评价id
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018-04-17
     * @apiSampleRequest                  http://qaserviceapi.xingfufit.cn/service/evaluate/detail
     * @apiSuccess        (返回值)           {json}                                      data
     * @apiSuccessExample {json}返回值详情（成功）
     {
     }

     * @apiSuccessExample {json}返回值详情（失败）
    {
    "code": 0,
    "status": "error",
    "message": "参数错误"
    }
     */
    public function actionDetail()
    {
        $id = Yii::$app->request->get('id', 0);
        if ($id <= 0) {
            return $this->error('参数错误');
        }

        $infoRs = EvaluateList::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();
        // var_dump($infoRs);
        if (!isset($infoRs['id'])) {
            return $this->error('评价信息未找到');
        }
        $evaluate =EvaluateView::find()->alias('e')->where(['e.id'=>$id])->all();
        return $evaluate;

    }
    /**
     * @api               {GET}           /service/evaluate/view            会员评价课种详情
     * @apiVersion        1.0.0
     * @apiName           2会员评价课种详情
     * @apiGroup          evaluate
     * @apiDescription    2会员评价课种详情
     * <br />
     * @apiParam  {string}            id               评价id
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018-04-17
     * @apiSampleRequest                  http://qaserviceapi.xingfufit.cn/service/evaluate/view
     * @apiSuccess        (返回值)           {json}                                      data
     * @apiSuccessExample {json}返回值详情（成功）
     {}

     * @apiSuccessExample {json}返回值详情（失败）
    {
    "code": 0,
    "status": "error",
    "message": "参数错误"
    }
     */
    public function actionview()
    {
        $id = Yii::$app->request->get('id', 0);
        if ($id <= 0) {
            return $this->error('参数错误');
        }
        $evaluate =EvaluateView::find()->alias('e')->where(['e.id'=>$id])->all();
        return $evaluate;

    }
    /**
     * @api               {GET}           /service/evaluate/index            消息课程评价列表
     * @apiVersion        1.0.0
     * @apiName           3消息课程评价列表
     * @apiGroup          evaluate
     * @apiDescription    3消息课程评价列表
     * <br />
     * @apiParam  {string}            id               评价id
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018-04-17
     * @apiSampleRequest                  http://qaserviceapi.xingfufit.cn/service/evaluate/index
     * @apiSuccess        (返回值)           {json}                                      data
     * @apiSuccessExample {json}返回值详情（成功）
     {}

     * @apiSuccessExample {json}返回值详情（失败）
    {
    "code": 0,
    "status": "error",
    "message": "参数错误"
    }
     */

    public function actionIndex()
    {
        $query = EvaluateList::find()
            ->alias('e')
            ->joinWith('groupClass gc', false)
            ->where(['in', 'gc.coach_id', $this->employeeId])
            ->orderBy('id desc');
        return new ActiveDataProvider(['query' => $query]);

    }
}
