<?php

namespace service\models;

use common\models\Func;

class LeaveRecordView extends \common\models\LeaveRecord
{
    public function fields()
    {
        return [
            'id',

            'member_id' => function ($model) {
                return $model->leave_employee_id;
            },

            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'pic' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'pic');
            },

            'sex' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'sexname');
            },

            'age' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'age');
            },

            'mobile' => function ($model) {
                return Func::getRelationVal($model, 'member', 'mobile');
            },

            'member_card' => function ($model) {
                return Func::getRelationVal($model, 'memberCard', 'cardCategory', 'card_name');
            },

            'card_number' => function ($model) {
                return Func::getRelationVal($model, 'memberCard', 'card_number');
            },

            'leave_property',

            'leave_remain' => function ($model) {
                if($card = Func::getRelationVal($model, 'memberCard')){
                    if($card->leave_total_days){
                        $hadLeave = self::find()->where(['member_card_id'=>$model->member_card_id])->sum('leave_length');
                        return ($card->leave_total_days - $hadLeave) . '天';
                    }elseif($card->leave_days_times){
                        $arr = $card->leave_days_times;
                        return isset($arr[0][1]) ? $arr[0][1] . '次' : '';
                    }
                }
                return '';
            },

            'leave_start_time' => function ($model) {
                return date('Y-m-d', $model->leave_start_time);
            },

            'leave_end_time' => function ($model) {
                return date('Y-m-d', $model->leave_end_time);
            },
            'venue_name' => function ($model) {
                return Func::getRelationVal($model, 'member','venue', 'name');
            },

            'leave_length',

            'note',

            'type',
            'reject_note'
        ];
    }
}