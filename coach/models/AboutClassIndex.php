<?php

namespace coach\models;

use common\models\Func;

class AboutClassIndex extends \common\models\AboutClass
{
    public function fields()
    {
        return [
            'id',

            'member_id',

            'card_number' => function ($model) {
                return Func::getRelationVal($model, 'memberCard', 'card_number');
            },

            'start' => function ($model) {
                return date('m月d日 H:i', $model->start);
            },

            'status',

            'is_read',

            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'coach_name' => function ($model) {
                return Func::getRelationVal($model, 'employee', 'name');
            },

            'pic' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'pic');
            },

            'course_name' => function ($model) {
                return Func::getRelationVal($model, 'memberCourseOrderDetails', 'course_name');
            },

            'class_length' => function ($model) {
                return Func::getRelationVal($model, 'memberCourseOrderDetails', 'class_length');
            },

            'time' => function ($model) {
                return date('H:i', $model->create_at);
            },

            'class_date',

        ];
    }
}