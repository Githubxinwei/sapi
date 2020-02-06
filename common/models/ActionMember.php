<?php

namespace common\models;

use common\models\relations\ActionRelations;
use function GuzzleHttp\Psr7\str;
use yii\base\InvalidParamException;
use common\models\base\ActionImages;
class ActionMember extends \common\models\base\Action
{
    use ActionRelations;
    public function fields()
    {
        $fiele = [];
        $fiele['action_id'] = 'id';
        $fiele['ssentials'] = 'ssentials';
        $fiele['unit'] = 'unit';
        if ($this->unit == 2 || $this->unit == 3){
            $fiele['energy'] = 'energy';
        }
        $fiele['url'] = function($model){
            $images = $model->images;
            $result = [];
            if(is_array($images) && !empty($images)){
                foreach ($images as $k => $v){
                    $result[$k]['url'] = $v['url'];
	                $result[$k]['describe'] = $v['describe'];
                }
            }else{
                //TODO 不为数组的逻辑
                return ['url'=>Func::getRelationVal($model, 'images', 'url'),'describe'=>Func::getRelationVal($model, 'images', 'describe')];
            }
            return array_values(array_filter($result));
             };

        return $fiele;

    }
}