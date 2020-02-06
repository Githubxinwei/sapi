<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/17
 * Time: 上午 09:37
 */
namespace common\models;

use common\models\relations\EvaluateRelations;
use Yii;

class Evaluate extends \common\models\base\Evaluate
{
    use EvaluateRelations;
}