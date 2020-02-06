<?php
namespace common\models\relations;

use common\models\Region;

trait OrganizationRelations
{

    public function getRegion()
    {
        return $this->hasOne(Region::className(),['id'=>'area']);
    }

}