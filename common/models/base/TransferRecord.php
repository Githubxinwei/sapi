<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%transfer_record}}".
 *
 * @property string $id
 * @property string $member_card_id
 * @property string $to_member_id
 * @property string $from_member_id
 * @property string $transfer_time
 * @property integer $times
 * @property string $path
 * @property string $transfer_price
 * @property string $register_person
 * @property string $note
 * @property string $cashier_number
 * @property string $cashier
 * @property string $cash_payment
 * @property string $bank_card_payment
 * @property string $network_payment
 * @property string $member_card
 * @property string $discount
 * @property string $coupon
 * @property string $transfer
 * @property string $other
 * @property string $integral
 * @property string $spare
 */
class TransferRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%transfer_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_card_id', 'to_member_id', 'from_member_id', 'transfer_time', 'path'], 'required'],
            [['member_card_id', 'to_member_id', 'from_member_id', 'transfer_time', 'times'], 'integer'],
            [['path', 'spare'], 'string'],
            [['transfer_price', 'cash_payment', 'bank_card_payment', 'network_payment', 'member_card', 'discount', 'coupon', 'transfer', 'other', 'integral'], 'number'],
            [['register_person', 'note', 'cashier_number', 'cashier'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'member_card_id' => '会员卡id',
            'to_member_id' => '会员id',
            'from_member_id' => '转让人id',
            'transfer_time' => '转让时间',
            'times' => '剩余次数',
            'path' => '卡的转让经历(json)',
            'transfer_price' => '转让金额',
            'register_person' => '登记人',
            'note' => '转让原因',
            'cashier_number' => '收银单号',
            'cashier' => '收银员',
            'cash_payment' => '现金支付',
            'bank_card_payment' => '银行卡支付',
            'network_payment' => '网络支付',
            'member_card' => '会员卡（余额）支付',
            'discount' => '折扣折让',
            'coupon' => '优惠卷',
            'transfer' => '转账',
            'other' => '其他',
            'integral' => '积分',
            'spare' => '备用(json) ',
        ];
    }
}
