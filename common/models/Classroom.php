<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/5 0005
 * Time: 11:19
 */

namespace common\models;


class Classroom extends \common\models\base\Classroom
{

    /**
     * 该教室座位数
     * @return int|string
     */
    public function getSeat_num()
    {
        return Seat::find()->where(['classroom_id'=>$this->id])->count();
    }
}