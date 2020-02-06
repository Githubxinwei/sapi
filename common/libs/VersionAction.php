<?php
namespace common\libs;

use common\models\Version;
use yii\base\Action;

class VersionAction extends Action
{
    public function run($name)
    {
        return Version::findOne(['name'=>$name]);
    }
}