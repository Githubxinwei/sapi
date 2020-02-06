<?php

namespace service\models;

use common\models\Func;

class EntryRecord extends \common\models\EntryRecord
{
    public function fields()
    {
        return [
            'id',

            'entry_time',

            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'card_number' => function ($model) {
                return Func::getRelationVal($model, 'memberCard', 'card_number');
            },

            'mobile' => function ($model) {
                return Func::getRelationVal($model, 'member', 'mobile');
            },

            'employee' => function ($model) {//会籍顾问
                return Func::getRelationVal($model, 'member', 'employee', 'name');
            },

            'coach' => function ($model) {//私教
                return Func::getRelationVal($model, 'memberCard', 'memberCourseOrder', 'employeeS', 'name');
            },
        ];
    }
}