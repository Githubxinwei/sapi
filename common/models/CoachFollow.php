<?php
/**
 * Created by PhpStorm.
 * User: 张东旭
 * Date: 2018/5/7
 * Time: 15:09
 */
namespace common\models;

use common\models\relations\CoachFollowRelations;

class CoachFollow extends \common\models\base\CoachFollow
{
    use CoachFollowRelations;
}