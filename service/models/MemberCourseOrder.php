<?php

namespace service\models;

use common\models\Func;

class MemberCourseOrder extends \common\models\MemberCourseOrder
{
    public function fields()
    {
        return [
                'id',
                'product' => function($model)
                {
                    return $model->product_name;
                },
                'consumption' => function($model)
                {
                    $data[] = $model->product_name;
                    $data[] = $model->money_amount;
                    $data[] = date('m-d',$model->create_at);
                    return $data;
                },
            ];

    }
}