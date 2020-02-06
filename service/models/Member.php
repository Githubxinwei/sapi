<?php

namespace service\models;

use common\models\Func;

class Member extends \common\models\Member
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
                return Func::getRelationVal($model, 'memberDetails', 'sex') == 1 ? '男' : '女';
            },

            'age' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'age');
            },

            'mobile',

            'id_card' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'id_card');
            },

            'pay_status' => function($model)
            {
//                return 1;
                $var = Func::getRelationVal($model,'memberCourseOrder','pay_status');
                if($var == 1)
                {
                    return '已购私课';
                }else{
                    return '未购私课';
                }
            }
        ];
    }
}