<?php

namespace common\models;

use common\models\Action;
use common\models\base\ActionImages;
use common\models\MemberAction;
use common\models\relations\TrainStagesRelations;
use function GuzzleHttp\Psr7\str;
use yii\base\InvalidParamException;

class MemberTemplate extends \common\models\base\MemberTemplate
{
    public function fields()
    {
        return [
            'id',
            'class_id',
            'coach_id',
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
		                    $data['stages'][$k]['main'][$k1]['ssentials'] = Action::findOne(intval($v1['action_id']))->ssentials;
		                    $data['stages'][$k]['main'][$k1]['unit'] = Action::findOne(intval($v1['action_id']))->unit;
		                    if($data['stages'][$k]['main'][$k1]['unit'] == 2 || $data['stages'][$k]['main'][$k1]['unit'] == 3)
		                    {
			                    $data['stages'][$k]['main'][$k1]['energy'] = Action::findOne(intval($v1['action_id']))->energy;
		                    }
		
		                    $data['stages'][$k]['main'][$k1]['url'] = ActionImages::find()
			                    ->select(['url','describe'])
			                    ->where(['aid'=>$v1['action_id'],'type'=>2])
			                    ->asArray()
			                    ->all();
		
		                    $var = MemberAction::find()
			                    ->where(['action_id'=>$v1['action_id']])
			                    ->andWhere(['stage_id'=>$v['id']])
			                    ->andWhere(['template_id'=>$data['id']])
			                    ->andWhere(['class_id'=>$model->class_id])
			                    ->asArray()
			                    ->all();
		                    if(!empty($var))
		                    {
			                    foreach ($var as $k2 =>$v2)
			                    {
				                    $data['stages'][$k]['main'][$k1]['photoArr'] = (empty(json_decode(json_decode($v2['url'],true),true)))?[]:json_decode(json_decode($v2['url'],true),true);
				                    $data['stages'][$k]['main'][$k1]['action_number'] = (empty(json_decode(json_decode($v2['action_number'],true),true)))?[]:json_decode(json_decode($v2['action_number'],true),true);
				                    $data['stages'][$k]['main'][$k1]['content'] = $v2['content'];
				                    $data['stages'][$k]['main'][$k1]['use'] = 1;
			                    }
		                    }
	                    }
                }

                return $data;
            }

        ];
    }
}