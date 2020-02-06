<?php

namespace common\models\info;

use common\models\Course;
use common\models\Func;
use yii\helpers\ArrayHelper;

class BindPack extends \common\models\BindPack
{
    public function fields()
    {
        $fields = parent::fields();

        $fields['course_name'] = function($model){
            if($model->polymorphic_type == 'class'){
//                return $model->polymorphic_ids;
//                return Course::find()->select('name')->where(['id'=>empty($model->polymorphic_id)?$model->polymorphic_ids:$model->polymorphic_id])->createCommand()->rawSql;
                return ArrayHelper::getColumn(Course::find()->select('name')->where(['id'=>empty($model->polymorphic_id)?$model->polymorphic_ids:$model->polymorphic_id])->all(), 'name');
            }else{
                return Func::getRelationVal($model, 'chargeClass', 'name');
            }
        };

        return $fields;
    }


}