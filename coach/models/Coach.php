<?php

namespace coach\models;

class Coach extends \common\models\Employee
{
    public function fields()
    {
        return [
            'id',

            'pic',

            'name',

            'age' => function($model){
                return $model->age ?: '';
            },

            'work_time' => function($model){
                return $model->work_time ?: '';
            },
        ];
    }
}