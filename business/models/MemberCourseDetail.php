<?php

namespace business\models;

use Yii;
use common\models\Func;

class MemberCourseDetail extends \common\models\MemberCourseOrder
{
    public function fields()
    {
        return [
            'id',

            'pic' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'pic');
            },

            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'card' => function ($model) {
                return Func::getRelationVal($model, 'memberCardS', 'card_number');
            },

            'course' => function ($model) {
                return Func::getRelationVal($model, 'memberCourseOrderDetailsOne', 'course_name');
            },

            'fee' => function ($model) {
                return number_format($model->money_amount / $model->course_amount, 2);
            },

            'count' => function ($model) {
                return number_format($model->money_amount, 2);
            },

            'time' => function ($model) {
                return date('Y-m-d', $model->create_at);
            },

            'status' => function ($model) {
                $status = 0;
                if (($model->course_type == 1 || is_null($model->course_type)) &&
                    $model->money_amount > 0
                ) {
                    $status = 1;
                }
                return $status;
            }
        ];
    }
}