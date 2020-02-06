<?php

namespace service\models;

use common\models\Func;

class CoachFollow extends \common\models\CoachFollow
{
    public function fields()
    {
        return [
        	'id',
        	'create_at' => function($model){
        		return date("Y-m-d",$model->create_at);
        	},
			'category' =>function($model){
				$a = Func::getRelationVal($model->category);
				switch ($a)
				{
					case 1:
						return "QQ";
						break;
					case 2:
						return "微信";
						break;
					case 3:
						return "电话";
						break;
					case 4:
						return "短信";
						break;
					default:
						return "未知途径";
				}
			},
        	'content',
        	'name' => function($model){
                return Func::getRelationVal($model, 'employee', 'name');
        	},        	
        ];
    }
}