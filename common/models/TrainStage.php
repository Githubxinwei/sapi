<?php

namespace common\models;

use common\models\Action;
use common\models\base\ActionImages;
use function GuzzleHttp\Psr7\str;
use yii\base\InvalidParamException;

class TrainStage extends \common\models\base\TrainStage
{

    public function fields()
    {
        return [
            'id',
            'title',
            'main'=>function($model)
            {
                $var = $model->main;
                foreach ($var as $key=>$value){
                    $main =[];
                    foreach ($value as $k=>$v){
                        $v = (array)$v;
                        $main[$k]['action_id'] = intval($v['action_id']);
                        $main[$k]['title'] = Action::findOne(intval($v['action_id']))->title;
                        $main[$k]['energy'] = Action::findOne(intval($v['action_id']))->energy;
                        $main[$k]['number'] = intval($v['number']);
                        $main[$k]['url'] = ActionImages::find()
                            ->select(['url'])
                            ->where(['aid'=>$v['action_id']])
                            ->andWhere(['NOT',['url'=>null]])
                            ->asArray()
                            ->all();

                    }
                    $data[$key] = $main;
                }
                foreach ($data as $k1 =>$v1)
                {
                    return $v1;
                }
            },


        ];
    }

}