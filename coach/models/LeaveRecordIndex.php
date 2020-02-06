<?php

namespace coach\models;

use common\models\Func;

class LeaveRecordIndex extends \common\models\LeaveRecord
{
    public function fields()
    {
        return [
            'id',

            'member_id' => function ($model) {
                return $model->leave_employee_id;
            },

            'card_number' => function ($model) {
                return Func::getRelationVal($model, 'memberCard', 'card_number');
            },

            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'leave_property',

            'leave_end_time' => function ($model) {
                return date('m月d日', $model->leave_end_time);
            },

            'is_read',

            'time' => function ($model) {
                return date('H:i', $model->create_at);
            },

            'leave_length',
        ];
    }
}