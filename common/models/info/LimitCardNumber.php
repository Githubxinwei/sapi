<?php

namespace common\models\info;

use common\models\CardDiscount;
use common\models\Organization;
use yii\helpers\ArrayHelper;

class LimitCardNumber extends \common\models\LimitCardNumber
{
    public function fields()
    {
        $fields = parent::fields();
        $fields['venue_name'] = function ($model) {
            $venue_ids = $model->venue_ids;
            if(!empty($model->venue_id)) $venue_ids[] = $model->venue_id;
            return ArrayHelper::getColumn(Organization::find()->select('name')->where(['id'=>$venue_ids])->all(), 'name');
        };
        $fields['discount'] = function ($model) {
            return CardDiscount::find()->select('discount,surplus')->where(['limit_card_id'=>$model->id])->all();
        };
        return $fields;
    }


}