<?php
/**
 * Created by PhpStorm.
 * User: zmk
 * Date: 2018/5/29
 * Time: 19:20
 */
namespace common\models;
use common\models\relations\AboutYardRelations;

class AboutYard extends \common\models\base\AboutYard
{
    use AboutYardRelations;
}