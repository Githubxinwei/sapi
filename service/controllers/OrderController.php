<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/5 0005
 * Time: 上午 11:20
 */

namespace service\controllers;
use service\models\Order;
use service\base\BaseController;
use Yii;
use common\models\Func;
use service\models\MemberDeposit;
use service\models\Employee;

use common\libs\ActiveDataProvider;
class OrderController extends BaseController
{

    /**
     * @api {get} /business/order/manage-ment  订单管理
     * @apiName        1订单展示
     * @apiGroup       order
     * @apiDescription   订单展示
     * @apiParam  {string}            venue_id   场馆id   用于筛选
     * @apiParam  {string}            start      开始时间   用于筛选  不传值默认当天
     * @apiParam  {string}            end        结束时间   用于筛选
     * @apiParam  {string}            status     订单状态   用于筛选
     * @apiParam  {string}            note       业务行为   用于筛选
     * @apiDescription   订单管理
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/05
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/order/manage-ment?accesstoken=000_1518243940
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
     *  {
    "message": "",
    "code": 1,
    "status": 200,
    "data": {
    "items": [
    {
    "id": "1554",                        //订单id
    "order_number": "1510121494247185",
    "venue_name": "大学路舞蹈健身馆",      //售卖场馆
    "member_name": "吉培俊",
    "note": "办卡",                       //业务行为
    "product_name": "DXDT24MD",
    "total_price": "2559.00",            //订单金额
    "employee_name": "王春营",
    "single": 1,
    "pay_money_time": "1510121494",
    "status": 2                          //订单状态：1未付款；2已付款；3其他状态；
    "other_note": null,
    "pay_money_mode": 9,
    "payee_name": "段海然"
    }
    ]
    "extra": {
    "sum": "164180.00"
    },
    }

    }
     * * @apiSuccessExample {json}返回值详情（失败）
    {
    "message": "",
    "code": 0,
    "status": 422,
    "data": []
    }
     * */
    public function actionManageMent(){
        $venue_id = Yii::$app->request->get('venue_id', 0);
        $status = Yii::$app->request->get('status', 0);
        $note =  Yii::$app->request->get('note', 0);
        $start = Yii::$app->request->get('start', 0) ?: date('Y-m-d',time()).' 00:00:00';
        $end = Yii::$app->request->get('end', 0);
        $start = strtotime($start);
        $end = $end?$end==$start ? $start+86399 :   strtotime($end)+86399:$start+86399;
        $query = Order::find()->andWhere(['between', 'order_time', $start, $end]);
        if (!$venue_id){
            $venue_id=Yii::$app->params['authVenueIds'];
        }
        $query->andWhere(['venue_id'=>$venue_id]);
        if ($status) $query->andWhere(['status'=>$status]);
        if ($note) $query->andWhere(['like','note',$note]);
        if(empty($query)) return [];
        $querySum =clone $query;
        return new ActiveDataProvider(['query' => $query,'extra'=>['sum'=>$querySum->sum('total_price')]]);
    }
    /**
     * @api {get} /service/order/details 订单详情
     * @apiName        2订单详情展示
     * @apiDescription   订单展示
     * @apiGroup       order
     * @apiParam  {string}            orderid   订单id   必传
     *  @apiDescription   订单详情
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/05
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/order/details?accesstoken=000_1518243940&id=1579
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
     * {
    "message": "",
    "code": 1,
    "status": 200,
    "data": {
    "items": [
                {
                        "id": "1554",                        //订单id
                        "order_number": "1510121494247185",  //订单编号
                        "venue_name": "大学路舞蹈健身馆",      //售卖场馆
                        "member_name": "吉培俊",              //购买人
                        "note": "办卡",                       //业务行为
                        "card_name": "DXDT24MD",          //会员卡名称 或者 私教产品
                        "total_price": "2559.00",            //订单金额
                        "employee_name": "王春营",           //售卖人
                        "create_name": "段海然",             //操作人
                        "single": 1,
                        "pay_money_time": "1510121494",     //日期
                        "status": 2                          //订单状态：1未付款；2已付款；3其他状态 4退款申请 ；
                        "other_note": null,                  //订单备注
                        "pay_money_mode": 9,                 //付款途径：1现金；2支付宝；3微信；4pos刷卡；
                        "many_pay_mode": null                //多付款方式1.现金2.微信3.支付宝4.建设分期5.广发分期6.招行分期7.借记卡8.贷记卡
                }
     *        ]
     *      }
     * }
     *
          @apiSuccessExample {json}返回值详情（失败）
        {
        "message": "",
        "code": 0,
        "status": 422,
        "data": []
        }
     */
    public function actionDetails($orderid){
        $query = Order::findOne($orderid);
        return $query;
    }
    /**
     *
     * @api {post} /business/order/apply-refund?accesstoken=000_1518243940&orderId=2511  申请退款
     * @apiName        3申请退款
     * @apiDescription   申请退款
     * @apiGroup       order
     * @apiParam  {string}            orderid   订单id   必传
     * @apiParam  {string}            refundNote   退款原因   必传
     *  @apiDescription   申请退款
     *  <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/03
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/order/details?accesstoken=000_1518243940&id=1579
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
            {
            "message": "退款申请提交成功！",
            "code": 1,
            "status": 200,
            "data": {
            "message": "退款申请提交成功！"
            }
            }

    @apiSuccessExample {json}返回值详情（失败）
            {
            "name": "Not Found",
            "message": "退款原因为必填项！",
            "code": 0,
            "status": 404,
            "type": "yii\\web\\NotFoundHttpException"
            }
     * */
    public function actionApplyRefund(){
        $post  = \Yii::$app->request->post();
        $orderTwo = Order::find()->where(['id'=>$post['orderId']])
            ->andWhere(['and',['consumption_type'=>'deposit'],['note'=>'订金']])
            ->asArray()
            ->one();
        if(!empty($orderTwo)){
            $deposit = MemberDeposit::findOne(['member_id'=>$orderTwo['member_id']]);
            if ($deposit['is_use'] ==2){
                return $this->error('该会员已经用过订金,不能退款');
            }
        }
        if (!isset($post['refundNote'])) return $this->error('退款原因为必填项！');
        $order = Order::findOne($post['orderId']);
        if(!empty($order)){
            $adminModel = Employee::find()->where(['admin_user_id'=>\Yii::$app->user->identity->id])->all();
            $order->create_id   = isset($adminModel[0]['id'])?$adminModel[0]['id']:0;     //操作人
            $order->status      = 4;                      //退款
            $order->refund_note = $post['refundNote'];    //退款理由
            $order->apply_time  = time();                 //申请退款时间
            if($order->save()){
                return $this->success('退款申请提交成功！');
            }else{
                return $this->error('退款申请提交失败！');
            }
        }
        return $this->error('无权限！');
    }

    /**
     * @api {get} /business/order/refund-approval  退款审批列表
     * @apiName        1卡种审批列表
     * @apiGroup       order
     * @apiParam  {string}            type                  1待处理 2已处理
     * @apiParam  {string}            keyword               搜索关键词 /订单id 操作人
     * @apiDescription   卡种审批列表
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/02/28
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/order/refund-approval
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
    "message": "",
    "code": 1,
    "status": 200,
    "data": {
    "items": [
    {
    {
    "id": "3368",//订单id
    "total_price": "1000.00", //金额
    "apply_time": "1517827143",//申请日期
    "name": null,//申请人
    "create_id": "1451",
    "note": "订金",//订金退款
    "status": "4",//状态 5通过6拒绝 4为待处理
    "refund_note": "驱蚊器",
    "refuse_note": null,
    "employeem": null
    },
    }
    ],
    "_links": {
    "self": {
    "href": "http://www.api.com/business/order/refund-approval?accesstoken=000_1522771210&type=1&page=1"
    }
    },
    "_meta": {
    "totalCount": 3,
    "pageCount": 1,
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
    public function actionRefundApproval(){
        $type = Yii::$app->request->get('type', 1);
        if(!in_array($type, [1,2,3])) return $this->error('参数type错误');
        $keyword = Yii::$app->request->get('keyword', 0);
        $query = Order::find()->alias('o')->select('o.id,o.card_name,o.total_price,o.member_name as name,o.apply_time,ee.name as names,o.create_id,o.note,o.status,o.refund_note,o.refuse_note')
            ->joinWith('employee ee');
        if (empty($keyword)){
            if ($type == 1) $query->where(['o.status'=>4])->orderBy('o.apply_time DESC');
            if($type==2)$query->where(['o.status'=>[5,6]])->orderBy('o.review_time DESC,');
        }else{
            if ($keyword) $query->andWhere(['o.status'=>[4,5,6]])->andWhere(['or', ['like', 'o.member_name', $keyword], ['like', 'ee.name', $keyword],['like', 'o.note', $keyword]]);
        }
        $query->andWhere(['o.venue_id'=>Yii::$app->params['authVenueIds']])->asArray()->all();
        return new ActiveDataProvider(['query'=>$query]);
    }
    /**
     * @api {get} /business/order/approval-details  审批详情
     * @apiName        112审批详情
     * @apiGroup       order
     * @apiParam  {string}            orderid   订单id   必传
     * @apiDescription   12审批详情
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/02/28
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/order/approval-details
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
    "message": "",
    "code": 1,
    "status": 200,
    -"data": [
    -{
    "order_number": "1511944385964690",//订单编号
    "card_name": "PT常规课",//
    "total_price": "5000.00",
    "apply_time": "1519810071",//日期
    "name": "卢晶晶",//购买人
    "create_id": "1451",
    "note": "私教产品",//审批类型
    "status": "4",
    "refund_note": "dwadwadwadawdwad",//退款理由
    "refuse_note": null
    "venue_name": "大学路舞蹈健身馆",//场馆
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
    public function actionApprovalDetails($orderid){
        $query = Order::find()
            ->alias('o')
            ->select('o.approval_id,o.id,o.order_number,ee.name as names,o.review_time,o.card_name,o.venue_id,o.total_price,
            o.apply_time,o.member_name as name,o.create_id,o.note,o.status,o.refund_note,o.refuse_note,org.name as venue_name,o.member_id,md.pic,es.name as apname')
            ->joinWith('memberDetails md',false)
            ->joinWith('employee ee',false)
            ->joinWith('employeess es',false)
            ->joinWith('organization org',false)->where(['o.id'=>$orderid,'o.venue_id'=>Yii::$app->params['authVenueIds']])
            ->asArray()
            ->all();
        return $query;
    }
    /**
     * @api {get} /business/order/refund-approval-apply  退款审批同意拒绝
     * @apiName        1退款审批同意拒绝
     * @apiGroup       order
     * @apiParam  {string}            status = 2同意 3拒绝
     * @apiParam  {string}            orderid   订单id   必传
     * @apiParam  {string}            refuseNote 拒绝原因 拒接必传
     * @apiDescription   退款审批同意拒绝 同意post请求一个参数 拒绝post请求两个参数
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/02/28
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/order/refund-approval-apply
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
    "message": "拒绝退款申请！",
    "code": 1,
    "status": 200,
    -"data": {
    "message": "拒绝退款申请！"
    }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
    "name": "Not Found",
    "message": "该订单已更改！",
    "code": 0,
    "status": 404,
    "type": "yii\\web\\NotFoundHttpException"
    }
     */
    public function actionRefundApprovalApply(){
        $post  = \Yii::$app->request->post();
        if (!isset($post['orderid'])) return $this->error('订单id必填项！');
        $order =Order::findOne($post['orderid']);
        if ($order->status==5 || $order->status==6){
            return $this->error('该订单已更改！');
        }
        if ($post['status']==2){
            if (!empty($order)){
                $order->approval_id   = isset($this->user->employee->id) ?$this->user->employee->id:0;    //操作人
                $order->status      = 5;                      //同意退款申请
                $order->review_time = time();                 //同意退款时间
                if($order->save()){
                    return $this->success('同意退款申请！');
                }else{
                    return $this->error('失败！');
                }

            }
            return $this->error('无权限！');

        }elseif ($post['status']==3){
            if (!isset($post['refuseNote'])) return $this->error('拒绝原因为必填项！');
            if(!empty($order)){
                $order->approval_id   = isset($this->user->employee->id) ?$this->user->employee->id:0;  //操作人
                $order->status      = 6;                      //拒绝申请
                $order->refuse_note = $post['refuseNote'];    //拒绝原因
                $order->review_time = time();                 //拒绝退款时间
                if($order->save()){
                    return $this->success('拒绝退款申请！');
                }else{
                    return $order->errors;
                }
            }
            return $this->error(' 无权限！');
        }
    }

}