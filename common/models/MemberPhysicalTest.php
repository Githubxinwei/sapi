<?php

namespace common\models;

use common\models\relations\MemberPhysicalTestRelations;
use function GuzzleHttp\Psr7\str;
use yii\base\InvalidParamException;

class MemberPhysicalTest extends \common\models\base\MemberPhysicalTest
{
	use MemberPhysicalTestRelations;
	public function fields()
	{
		if ($this->type == 1){//体侧
		  $data = PhysicalTest::findOne(['id'=>$this->cid]);
		}elseif($this->type ==2){
			$data = FitnessAssessment::findOne(['id'=>$this->cid]);
		}

			return [
				'id',
				'name'=>function($model) use($data){
					return $data->title;
				},
				'unit'=>function($model) use($data){
					return $data->unit;
				},
				'type'=>function($model) use($data){
					if ($model->type == 1 && $data->type == 1){
						return 1;
					}else{
						return 0;
					}
				},
				'physical_value',
				'son'=>function($model){
					$data = MemberPhysicalTest::findAll(['pid'=>$model->cid,'type'=>$model->type]);
					return $data;
				}


			];

	}
}