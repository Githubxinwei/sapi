<?php

namespace common\models\info;

use common\models\Func;

class ApprovalDetails extends \common\models\ApprovalDetails
{

    public function fields()
    {
        return [
            'status',

            'describe',
            'approver_id',

            //'approval_process_id',

            'create_at',

            'update_at',

            'employee_name' => function ($model) {
                return Func::getRelationVal($model, 'employee', 'name');
            },

            'employee_pic' => function ($model) {
                return Func::getRelationVal($model, 'employee', 'pic');
            },

        ];
    }

}