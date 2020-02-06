<?php

namespace common\models;
use common\models\relations\CoachAssignMemberRecordRelations;
use Yii;

class CoachAssignMemberRecord extends \common\models\base\CoachAssignMemberRecord
{
    use CoachAssignMemberRecordRelations;
}