<?php

namespace common\models;

use common\models\relations\ActionCategoryRelations;
use function GuzzleHttp\Psr7\str;
use yii\base\InvalidParamException;

class ActionCategory extends \common\models\base\ActionCategory
{
    use ActionCategoryRelations;
    public static function getCateTree()
    {
        //查询顶级分类
        $data = self::find()->select('id,pid,title')->orderBy('pid asc')->asArray()->all();
        return $data;
    }
}