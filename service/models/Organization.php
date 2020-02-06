<?php
namespace service\models;


class Organization extends \common\models\Organization
{
    public function fields()
    {
        return [
            'id',

            'name',

            'style',

            'employee_count',
        ];
    }
}