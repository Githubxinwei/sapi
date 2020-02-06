<?php

namespace common\models\info;

use common\models\base\CardCategoryType;
use common\models\Func;
use yii\helpers\ArrayHelper;

class CardCategory extends \common\models\CardCategory
{
    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [

            'duration' => function ($model) {
                return $model->duration;
            },

            'deal',

            'sell_limit_card_number' => function ($model) {
                return LimitCardNumber::find()->where(['card_category_id'=>$model->id, 'status'=>[2,3]])->all();
            },

            'apply_limit_card_number' => function ($model) {
                return LimitCardNumber::find()->where(['card_category_id'=>$model->id, 'status'=>[1,3]])->all();
            },

            'bind_group' => function ($model) {
                return BindPack::find()->where(['card_category_id'=>$model->id, 'polymorphic_type'=>'class'])->all();
            },

            'bind_private' => function ($model) {
                return BindPack::find()->where(['card_category_id'=>$model->id, 'polymorphic_type'=>['birth', 'hs', 'pt']])->andWhere(['>', 'polymorphic_id', 0])->all();
            },

            'type_name' => function ($model) {
                return array_column(CardCategoryType::find()->where(['id'=>$model->category_type_id])->all(),'type_name')[0];
            },

        ]);
    }
}