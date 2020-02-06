<?php

namespace common\models;

use common\models\Action;
use common\models\base\ActionImages;
use common\models\MemberAction;
use common\models\relations\TrainStagesRelations;
use function GuzzleHttp\Psr7\str;
use yii\base\InvalidParamException;
use common\models\MemberCourseOrderDetails;
class MemberTemplaterecord extends \common\models\base\MemberTemplate
{
    public function fields()
    {

        return [
            'id',
            'class_id',
            'main' => function($model)
            {
                $data = json_decode($model->main,true);
	            foreach ($data['stages'] as $k => $v)
	            {
		            foreach ($v['main'] as $k1 => $v1)
		            {
			            unset($data['stages'][$k]['main'][$k1]['url']);
			            if ($v1['action_id'] == '-1'){
				            unset($data['stages'][$k]['main'][$k1]);
			            }
		            }
	            }

                foreach ($data['stages'] as $k => $v)
                {
                    foreach ($v['main'] as $k1 => $v1)
                    {
                        $memberAction = MemberAction::findOne(['action_id'=>$v1['action_id'],'class_id'=>$model->class_id,'stage_id'=>$v['id']]);
                        $data['stages'][$k]['main'][$k1]['mcontent'] = isset($memberAction['content']) ?empty($memberAction['content']) ? "":$memberAction['content'] :"" ;//动作评价
                        $data['stages'][$k]['main'][$k1]['murl'] = isset($memberAction['url']) ?empty($memberAction['url']) ? []:json_decode($memberAction['url'],true) :[] ;//动作评价图片
	                    if ($v1['unit']==1){
		                    $num = isset($memberAction['action_number']) ?empty($memberAction['action_number']) ? []:json_decode($memberAction['action_number'] ):[] ;//动作组数
	                    }else{
		                    $num = isset($memberAction['action_number']) ?empty($memberAction['action_number']) ? json_decode($memberAction['action_number']) : '' :''  ;//动作组数
	                    }
                        $data['stages'][$k]['main'][$k1]['action_number'] =$num ;
                    }
                }
                return $data;
            },
            'result' =>function($model){
                return MemberResult::find()->select('complete,calorie,motion,motion_qd,everyday,member_url')
                    ->where(['class_id'=>$model->class_id,'member_id'=>$model->member_id])->asArray()->all();
            },
	        'time'=>function($model){
        	$ab  = AboutClass::findOne($model->class_id);
        	$time  =  $ab->class_date;
        	$class_ids = $ab->class_id;
        	if (isset($class_ids) || empty($class_ids)){
        		$up = $down = '';
	        };
        	$da = AboutClass::find()->select('id as class_id,member_id')->where(['member_id'=>$model->member_id,'class_id'=>$class_ids,'status'=>4,'coach_id'=>$this->coach_id]);
        	$up = clone  $da;
	        $up = $up->andWhere(['<','start',$ab->start])->limit(1)->orderBy('start DESC')->asArray()->all();
	        $down = clone $da;
	        $down = $down->andWhere(['>','start',$ab->start])->limit(1)->orderBy('start ASC')->asArray()->all();
		
		        return ['up'=>$up,'time'=>$time,'down'=>$down];
        	
	        }

//            'course_name'=>function($model){
//                $name =  MemberCourseOrderDetails::find()
//                    ->alias('mcod')
//                    ->select('mcod.course_name')
//                    ->joinWith('aboutClass ac',false)
//                    ->where(['ac.id'=>$model->class_id])
//                    ->one();
//                return isset($name->course_name) ?$name->course_name:'暂无!';
//            },
//            'img'=>function($model) use(&$imgs){
//                foreach ($imgs as $key=>$value){
//                    if (!empty($value)){
//                        foreach ($value as $k=>$v){
//                            $a[]=$v;
//                        }
//                    }
//                }
//                return empty($a) ?[]:$a;
//            }
        ];
    }
}