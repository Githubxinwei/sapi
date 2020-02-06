<?php

namespace coach\models;

use common\models\Func;

class Sale extends \common\models\MemberCourseOrder
{
    public function fields()
    {
        return [

            'coach_name' => function ($model) {
                return Func::getRelationVal($model, 'employeeS', 'name');
            },

            'name' => function ($model) {
                return Func::getRelationVal($model, 'member', 'memberDetails', 'name');
            },

            'status' => function ($model) {
                return '成交';
            },

            'product_name',

            'course_amount',

            'money_amount',
        ];
    }
}