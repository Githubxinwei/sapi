<?php

namespace common\models;

use common\models\relations\ActionRelations;
use function GuzzleHttp\Psr7\str;
use yii\base\InvalidParamException;

class Action extends \common\models\base\Action
{
    use ActionRelations;
    public function fields()
    {
        return [
            'id',
            'title',
            'type' => function($model)
            {
                if($model->type == 0)
                {
                    return "其他";
                }
                if($model->type == 1)
                {
                    return "有氧";
                }
                if($model->type == 2)
                {
                    return "重量";
                }
            },
            'unit' => function($model)
            {
                if($model->type == 0)
                {
                    return  "组数";
                }
                if($model->type == 1)
                {
                    return "时间(min)";
                }
                if($model->type == 2)
                {
                    return "组数";
                }
            },
	        'uunits' => function($model)
		    {
			    return $model->unit;
		    },
            'url' => function($model)
            {
                $images = $model->images;
//                return $images;
                $result = [];
                if(is_array($images) && !empty($images)){
                    foreach ($images as $k => $v){
                        $result[$k] = $v['url'];
                    }
                }else{
                    //TODO 不为数组的逻辑
                    return Func::getRelationVal($model, 'images', 'url');
                }
                return array_values(array_filter($result));
            },
            'titles' => function($model)
            {
                $data = [];
                $category = $model->categorys;
                foreach ($category as $k =>$v)
                {
                        $data[]['titles'] = $v->title;
                }
                return array_column($data,'titles');
            }

        ];
    }


}