<?php

namespace group\models;

use common\models\Func;

class GroupClass extends \common\models\GroupClass
{
    public function fields()
    {
        return [
            'id',
            'start',
            'end',
            'class_date',
            'in_time',
            'out_time',
            'course_name' => function ($model) {
                if(\Yii::$app->language == 'en-us'){
                    return isset(\Yii::$app->params['class'][Func::getRelationVal($model, 'course', 'name')]) ?\Yii::$app->params['class'][Func::getRelationVal($model, 'course', 'name')] : Func::getRelationVal($model, 'course', 'name');
                }else{
                    return Func::getRelationVal($model, 'course', 'name');
                }
            },
            'seat_num' => function ($model) {
                $total_rows = Func::getRelationVal($model, 'seatType', 'total_rows');
                $total_columns = Func::getRelationVal($model, 'seatType', 'total_columns');
                $seat_num = (int)$total_rows * (int)$total_columns;
                return $seat_num;
            },
            'about_num',
	        'venueName'=>function($model){
        	return isset($model->organization->name) ? $model->organization->name : '暂无!';
	        }
        ];
    }
}