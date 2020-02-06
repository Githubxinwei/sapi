<?php

namespace common\models\info;

use common\models\ApprovalRole;
use common\models\Employee;
use common\models\Func;
use yii\helpers\ArrayHelper;

class Approval extends \common\models\Approval
{

    public function fields()
    {
        return [
            'id',

            'name',

            'polymorphic_id',

            'status',

            'number',

            'create_at',

            'create_name' => function ($model) {
                return Func::getRelationVal($model, 'employee', 'name');
            },

            'create_pic' => function ($model) {
                return Func::getRelationVal($model, 'employee', 'pic');
            },

            'create_venue' => function ($model) {
                return Func::getRelationVal($model, 'employee', 'venue', 'name');
            },

            'create_organization' => function ($model) {
                return Func::getRelationVal($model, 'employee', 'organization', 'name');
            },

            'create_position' => function ($model) {
                return Func::getRelationVal($model, 'employee', 'position');
            },

            'type_name' => function ($model) {
                return Func::getRelationVal($model, 'approvalType', 'type');
            },

            'details' => function ($model) {
                return ApprovalDetails::find()->where(['approval_id'=>$model->id,"type"=>1])->all();
            },

            'cc' => function ($model) {
                $employees = ArrayHelper::getColumn(ApprovalDetails::find()->where(['approval_id'=>$model->id,"type"=>2])->all(),'approver_id');
                return ArrayHelper::getColumn(Employee::find()->select('name')->where(['id'=>$employees])->all(), 'name');
            },


        ];
    }

}