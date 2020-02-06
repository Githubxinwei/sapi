<?php

namespace common\models;

use common\models\relations\AboutClassRelations;
use function GuzzleHttp\Psr7\str;
use yii\base\InvalidParamException;

class AboutClassrecord extends \common\models\base\AboutClass
{
    use AboutClassRelations;
}