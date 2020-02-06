<?php

namespace group\models;

use common\models\base\AboutClass;
use common\models\Func;
use common\models\Seat;

class MyGroupClass extends \common\models\GroupClass
{
    public function fields()
    {
        return [
            'id',
	        'seat_num' => function ($model) {
			    $total_rows = Func::getRelationVal($model, 'seatType', 'total_rows');
			    $total_columns = Func::getRelationVal($model, 'seatType', 'total_columns');
			    $seat_num = (int)$total_rows * (int)$total_columns;
			    return $seat_num;
		    },
	        'course_name' => function ($model) {
		        if(\Yii::$app->language == 'en-us'){
			        return isset(\Yii::$app->params['class'][Func::getRelationVal($model, 'course', 'name')]) ?\Yii::$app->params['class'][Func::getRelationVal($model, 'course', 'name')] : Func::getRelationVal($model, 'course', 'name');
		        }else{
			        return Func::getRelationVal($model, 'course', 'name');
		        }
	        },
	        'count'=>function ($model) {
		        $id = $model->id;
		        AboutClass::updateAll(['status'=>3], ['and',['class_id'=>$id],['type'=>2],['status'=>1],['or',['is_print_receipt'=>1],['<>','in_time',0]],['<=','start',time()]]);
		        AboutClass::updateAll(['status'=>4], ['and',['class_id'=>$id],['type'=>2],['status'=>3],['or',['is_print_receipt'=>1],['<>','in_time',0]],['<','end',time()]]);
		        AboutClass::updateAll(['status'=>6], ['and',['class_id'=>$id],['type'=>2],['status'=>1],['is_print_receipt'=>2],['in_time'=>0],['<','start',time()]]);
		        $data = AboutClass::find()->where(['class_id'=>$model->id,'type'=>2])->asArray()->all();
		        $appointment = $cancel = $absenteeism = $normal=[];
			        foreach ($data as $key=>$value) {
//				        if ($value['status']==1 || $value['status']==3 ||$value['status']==4 ) $appointment[] = $value;
				        if ($value['status']==2) $cancel[]=$value;
				        if ($value['status']==4) $normal[]=$value;
				        if ($value['status']==6||$value['status']==7) $absenteeism[]=$value;
			        }
			        $count['appointment'] = count($data);
		            $count['cancel'] = count($cancel);
		            $count['normal'] = count($normal);
		            $count['absenteeism'] = count($absenteeism);
		            return $count;
	        },
	        'click'=>function($model){
        	 return $model->end < time() ? true : false;
	        },
	        'venueName'=>function($model){
		        return isset($model->organization->name) ? $model->organization->name : '暂无!';
            },
	        'class_date',
	        'start',
	        'regiment_notes'
        ];
    }
}