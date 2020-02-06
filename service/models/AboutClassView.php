<?php

namespace service\models;

use common\models\Func;

class AboutClassView extends \common\models\AboutClass
{
    public function fields()
    {
	    $data =   MemberClassBeforeQuestion::findOne(['about_class_id'=>$this->id,'member_id'=>$this->member_id]);
	    return [
            'id',

            'member_id',

            'pic' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'pic');
            },

            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'sex' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'sexname');
            },

            'age' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'age');
            },

            'mobile' => function ($model) {
                return Func::getRelationVal($model, 'member', 'mobile');
            },

            'course_name' => function ($model) {
                return Func::getRelationVal($model, 'memberCourseOrderDetails', 'course_name');
            },

            'product_name' => function ($model) {
                return Func::getRelationVal($model, 'memberCourseOrderDetails', 'product_name');
            },

            'coach' => function ($model) {
                return Func::getRelationVal($model, 'employee', 'name');
            },

            'start' => function ($model) {
                return date('m月d日 H:i', $model->start);
            },

            'class_length' => function ($model) {
                return Func::getRelationVal($model, 'memberCourseOrderDetails', 'class_length');
            },

            'card_name' => function ($model) {
                return Func::getRelationVal($model, 'memberCard', 'cardCategory', 'card_name');
            },

            'card_number' => function ($model) {
                return Func::getRelationVal($model, 'memberCard', 'card_number');
            },

            'sell_coach' => function ($model) {
                return Func::getRelationVal($model, 'memberCourseOrderDetails', 'memberCourseOrder', 'employee', 'name');
            },

            'money_amount' => function ($model) {
                return Func::getRelationVal($model, 'memberCourseOrderDetails', 'memberCourseOrder', 'money_amount');
            },

            'course_amount' => function ($model) {
                return Func::getRelationVal($model, 'memberCourseOrderDetails', 'memberCourseOrder', 'course_amount');
            },

            'overage_section' => function ($model) {
                return Func::getRelationVal($model, 'memberCourseOrderDetails', 'memberCourseOrder', 'overage_section');
            },

            'deadline_time' => function ($model) {
                $deadline_time = Func::getRelationVal($model, 'memberCourseOrderDetails', 'memberCourseOrder', 'deadline_time');
                return $deadline_time ? date('Y-m-d', $model->memberCourseOrderDetails->memberCourseOrder->deadline_time) : '';
            },

            'cancel_time' => function ($model) {
                return $model->cancel_time ? date('m月d日 H:i', $model->cancel_time) : '';
            },

            'cancel_reason' => function ($model) {
                return $model->cancel_reason ?: '';
            },

            'month_cancel' => function ($model) {
                return (int)$model->monthCancel;
            },
	        'Inquiries'=>function($model) use($data){
        	if (empty($data)){
		        $id =  Func::getRelationVal($model, 'memberCourseOrderDetails', 'course_id');
		        return ClassBeforeQuestion::find()->where(['course_id'=>$id,'status'=>0])->orderBy('update_at DESC')->all();
	        }else{
        		$data = json_decode($data->answer_question);
        		$question = [];
        		foreach ($data as $key=>$value){
					if (isset($value->option)){
						$question[]=$value;
					}
		        }
		        return $question;
	        }
		       
	        },
	        'chick'=>function($model) use($data){
        	    return empty($data) ? 0 :1;
	        }

        ];
    }
}