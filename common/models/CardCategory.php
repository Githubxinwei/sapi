<?php
/**
 * Created by PhpStorm.
 * User: lihuien
 * Date: 2017/3/30
 * Time: 10:58
 */

namespace common\models;


class CardCategory extends \common\models\base\CardCategory
{
    use \common\models\relations\CardCategoryRelations;
    public function rules()
    {
        return [
            [['category_type_id', 'card_name', 'venue_id', 'create_id', 'deal_id'], 'required'],
            [['category_type_id', 'create_at', 'class_server_id', 'server_combo_id', 'times', 'count_method', 'sell_start_time', 'sell_end_time', 'attributes', 'total_store_times', 'venue_id', 'payment', 'payment_months', 'total_circulation', 'sex', 'age', 'transfer_number', 'create_id', 'sales_mode', 'missed_times', 'limit_times', 'active_time', 'status', 'leave_total_days', 'leave_total_times', 'person_times', 'leave_least_Days', 'deal_id', 'renew_unit', 'company_id', 'is_app_show', 'bring', 'card_type'], 'integer'],
            [['regular_renew_time', 'regular_transform_time'], 'safe'],
            [['original_price', 'sell_price', 'max_price', 'min_price', 'transfer_price', 'recharge_price', 'recharge_give_price', 'single_price', 'renew_price', 'offer_price', 'single', 'ordinary_renewal'], 'number'],
            [['service_pay_ids', 'validity_renewal', 'pic'], 'string'],
            [['card_name', 'recharge_start_time', 'recharge_end_time', 'another_name'], 'string', 'max' => 200],
        ];
    }
}