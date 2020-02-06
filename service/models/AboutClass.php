<?php

namespace service\models;

use Yii;
use common\models\Func;

class AboutClass extends \common\models\AboutClass
{

    public static function find()
    {
        return parent::find()->alias('ac')->joinWith('employee e')->where(['e.venue_id'=>Yii::$app->params['authVenueIds']]);
    }


}