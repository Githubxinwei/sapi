<?php

namespace service\models;

use common\models\Func;

class MemberCourseOrderCoachPrivateCourse extends \common\models\MemberCourseOrder
{
    public function fields()
    {
        return [
            'id' => function ($model) {
                return Func::getRelationVal($model, 'member', 'id');
            },

            'name' => function($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'sex' => function($model) {
                $sex = Func::getRelationVal($model, 'memberDetails', 'sex');
                return $sex == 1 ? '男' : ($sex == 2 ? '女' : '未知');
            },

            'pic' => function($model) {
                return Func::getRelationVal($model, 'memberDetails', 'pic');
            },

            // 'level',

            'mobile' => function ($model) {
                return Func::getRelationVal($model, 'member', 'mobile');
            },

            'time' => function($model) {
                $time = Func::getRelationVal($model, 'member', 'register_time');
                return $time > 0 ? date('Y-m-d', $time) : '';
            },

        ];
    }
}