<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $id
 * @property string $venue_id
 * @property string $member_id
 * @property string $card_category_id
 * @property string $total_price
 * @property string $order_time
 * @property string $pay_money_time
 * @property integer $pay_money_mode
 * @property string $sell_people_id
 * @property string $payee_id
 * @property string $create_id
 * @property integer $status
 * @property string $note
 * @property string $order_number
 * @property string $card_name
 * @property string $sell_people_name
 * @property string $payee_name
 * @property string $member_name
 * @property string $pay_people_name
 * @property string $company_id
 * @property string $merchant_order_number
 * @property string $consumption_type_id
 * @property string $consumption_type
 * @property string $deposit
 * @property string $cash_coupon
 * @property string $net_price
 * @property string $all_price
 * @property string $refund_note
 * @property string $refuse_note
 * @property string $apply_time
 * @property string $review_time
 * @property integer $is_receipt
 * @property integer $purchase_num
 * @property string $new_note
 * @property string $many_pay_mode
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['venue_id', 'member_id', 'card_category_id', 'order_number'], 'required'],
            [['venue_id', 'member_id', 'card_category_id', 'order_time', 'pay_money_time', 'pay_money_mode', 'sell_people_id', 'payee_id', 'create_id', 'status', 'company_id', 'consumption_type_id', 'apply_time', 'review_time', 'is_receipt', 'purchase_num'], 'integer'],
            [['total_price', 'deposit', 'cash_coupon', 'net_price', 'all_price'], 'number'],
            [['note', 'refund_note', 'refuse_note'], 'string'],
            [['order_number', 'merchant_order_number'], 'string', 'max' => 255],
            [['card_name', 'sell_people_name', 'payee_name', 'member_name', 'pay_people_name', 'consumption_type', 'new_note'], 'string', 'max' => 200],
            [['order_number'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'venue_id' => '售卖场馆id',
            'member_id' => '会员id',
            'card_category_id' => '卡种id',
            'total_price' => '总价',
            'order_time' => '订单创建时间',
            'pay_money_time' => '付款时间',
            'pay_money_mode' => '付款方式：1现金；2支付宝；3微信；4pos刷卡；',
            'sell_people_id' => '售卖人id',
            'payee_id' => '收款人id',
            'create_id' => '操作人id',
            'status' => '订单状态：1未付款；2已付款；3其他状态；',
            'note' => '备注',
            'order_number' => '订单编号',
            'card_name' => '卡名称',
            'sell_people_name' => '售卖人姓名',
            'payee_name' => '收款人姓名',
            'member_name' => '购买人姓名',
            'pay_people_name' => '付款人姓名',
            'company_id' => '公司id',
            'merchant_order_number' => '商户单号',
            'consumption_type_id' => '多态id',
            'consumption_type' => '多态类型',
            'deposit' => '订单：定金',
            'cash_coupon' => '订单：代金券',
            'net_price' => '订单：实收价格',
            'all_price' => '订单：商品总价格',
            'refund_note' => '退款理由',
            'refuse_note' => '拒绝原因',
            'apply_time' => '申请退款时间',
            'review_time' => '审批时间',
            'is_receipt' => '0、未开票；1、已开票',
            'purchase_num' => '购买数量(1:私课节数2:卡数量)',
            'new_note' => '新备注',
//            'many_pay_mode' => '多付款方式1.现金2.微信3.支付宝4.建设分期5.广发分期6.招行分期7.借记卡8.贷记卡',
        ];
    }
}
