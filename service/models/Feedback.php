<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/9 0009
 * Time: 下午 2:06
 */

namespace service\models;
use common\models\Func;

class Feedback extends \common\models\Feedback
{
    public function fields()
    {
        return [
            'id',
            'type_id',
            'from',
            'company_id',
            'venue_id',
            'venue_name'=>function ($model) {
                $venue = Organization::findOne($model->venue_id);
                return $venue->name;
            },
            'user_info'=>function ($model) {
                $member = Member::findOne($model->user_id);
                return $member;
            },
            'content',
            'occurred_at',
            'created_at',
            'updated_at',
            'pics'=>function ($model) {
                return $model->pics;
            },
            'reply_time'
            ];
    }
}