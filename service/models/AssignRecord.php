<?php

namespace service\models;

use common\models\Func;

class AssignRecord extends \common\models\CoachAssignMemberRecord
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

            'sex' => function ($model) {
                $temp = Func::getRelationVal($model, 'memberDetails', 'sex');
                return $temp == 1 ? '男' : ($temp == 2 ? '女' : '未知');
            },

            'age' => function ($model) {
                $birthday = Func::getRelationVal($model, 'memberDetails', 'birth_time');
                $age = null;
                if (!empty($birthday)) {
                    $age = strtotime($birthday);
                    if ($age == false) {
                        return null;
                    }
                    list($y1, $m1, $d1) = explode('-', date('Y-m-d', $age));
                    list($y2, $m2, $d2) = explode('-', date('Y-m-d', time()));

                    $age = $y2 - $y1;
                    if ((int)($m2 . $d2) < (int)($m1 . $d1)) {
                        $age -= 1;
                    }
                }
                return $age;
            },

            'mobile' => function ($model) {
                return Func::getRelationVal($model, 'member', 'mobile');
            },

            'member_id',

            // 'coach_name' => function ($model) {
            //     return Func::getRelationVal($model, 'employee', 'name');
            // },

            'assign_name' => function ($model) {
                return Func::getRelationVal($model, 'management', 'name');
            },

            'assign_time' => function ($model) {
                return $model->create_at ? date('Y-m-d H:i:s', $model->create_at) : '';
            },
        ];
    }
}