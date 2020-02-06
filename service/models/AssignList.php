<?php

namespace service\models;

use common\models\Func;

class AssignList extends \common\models\CoachAssignMemberRecord
{
    public function fields()
    {
        return [
            'id',

            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'mobile' => function ($model) {
                return Func::getRelationVal($model, 'member', 'mobile');
            },

            'member_id',

            'card_number' => function ($model) {
                return Func::getRelationVal($model, 'memberCard', 'card_number');
            },

            'assign_name' => function ($model) {
                return Func::getRelationVal($model, 'management', 'name');
            },

            'assign_time' => function ($model) {
                return $model->create_at ? date('m-d H:i', $model->create_at) : '';
            },

            'status' => 'is_read',
        ];
    }
}