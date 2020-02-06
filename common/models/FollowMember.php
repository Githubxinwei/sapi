<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use common\models\relations\FollowMemberRelations;
class FollowMember extends \common\models\base\FollowMember
{
    use FollowMemberRelations;
    public function fields()
    {
        return [
            'id',
            'content',
            'title' => function($model)
            {
                return Func::getRelationVal($model, 'followWay', 'title');
            }
        ];
    }
}