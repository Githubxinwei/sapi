<?php

namespace common\models;

class Region extends \common\models\base\Region
{
    /**
     * 以数组形式返回该地区的完整名称 eg:['山西省', '太原市']
     * @return array
     */
    public function getNamearr()
    {
        $arr[] = $this->name;
        $parent_id = $this->parent_id;
        while ($parent_id){
            $parent = static::findOne(['id'=>$parent_id]);
            $arr[] = $parent->name;
            $parent_id = $parent->parent_id;
        }
        return array_reverse($arr);
    }
}