<?php

namespace service\models;

use common\models\Func;
use common\models\relations\MemberRelations;

class Mymember extends \common\models\base\Member
{
    use MemberRelations;
    public function fields()
    {
        return [
            'id',
            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },
            'content' => function($model){
                $data = Func::getRelationVal($model, 'followMember', 'content');
                if(!empty($data))
                {
                    return $data;
                }else{
                    return "暂无";
                }
            },
            'create_at' => function($model){
                $data = Func::getRelationVal($model, 'followMember', 'create_at');
                if(!empty($data))
                {
                    return $data;
                }else{
                    return "暂无";
                }
            }

        ];
    }
}