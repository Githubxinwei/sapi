<?php

namespace business\models;

use Yii;
use common\models\Func;

class MemberCourseOrder extends \common\models\MemberCourseOrder
{

//    public static function find()
//    {
//        return parent::find()->alias('mco')->joinWith('employeeS e')->joinWith('order o')->where(['e.venue_id'=>Yii::$app->params['authVenueIds']]);
//    }

    public function fields()
    {
        return [
            'id',

            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'business_remarks',

            'product_name',

            'money_amount',

            'create_at',

        ];
    }
}