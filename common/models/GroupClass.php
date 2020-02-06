<?php

namespace common\models;
use common\models\relations\GroupClassRelations;

class GroupClass extends \common\models\base\GroupClass
{
    use GroupClassRelations;

    /**
     * 该课预约人数
     * @return int|string
     */
    public function getAbout_num()
    {
        return AboutClass::find()->where(['and',['class_id'=>$this->id],['type'=>2],['!=','status',2]])->count();
    }
}