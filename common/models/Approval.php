<?php

namespace common\models;

use common\models\relations\ApprovalRelations;

class Approval extends \common\models\base\Approval
{
    use ApprovalRelations;
}