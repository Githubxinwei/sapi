<?php
/**
 * Created by PhpStorm.
 * User: lihuien
 * Date: 2018/2/5
 * Time: 17:16
 */

namespace coach\models;


class Schedule extends \common\models\Schedule
{
    public function fields()
    {
        return [
            'name',

            'start',

            'end',

            'describe'
        ];
    }
}