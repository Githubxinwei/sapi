<?php

namespace business\models;

use common\models\Func;
use Yii;

class Order extends \common\models\Order
{


    public function fields()
    {
        return [
            'id',//订单id

            'order_number',//订单编号

            'venue_name' => function ($model) {
                return Func::getRelationVal($model, 'organization', 'name');
            },//售卖场馆

            'member_name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },//购买人

            'note', //业务行为

            'card_name',//会员卡名称 或者 私教产品，

            'total_price',//订单金额

            'employee_name' => function ($model) {
                return Func::getRelationVal($model,'employeeS', 'name');
            },//售卖人

            'single' => function ($model) {
                return Func::getRelationVal($model, 'memberCard', 'cardCategory', 'single');
            },
            'payee_name', //操作人

            'pay_money_time',//日期
            'status',//订单状态：1未付款；2已付款；3其他状态 4退款申请 ；
            'other_note',//订单备注
            'pay_money_mode'  =>function($model){
                return $pay_money_mode = ($model->pay_money_mode === 0) ?  null : $model->pay_money_mode;
            },//付款途径：1现金；2支付宝；3微信；4pos刷卡；
            'many_pay_mode',//多付款方式1.现金2.微信3.支付宝4.建设分期5.广发分期6.招行分期7.借记卡8.贷记卡

        ];
    }

    public static function findOne($id)
    {
        return parent::find()->where(['id'=>$id, 'venue_id'=>\Yii::$app->params['authVenueIds']])->one();
    }
}