<?php
/**
 * Created by PhpStorm.
 * User: lihuien
 * Date: 2018/2/6
 * Time: 11:20
 */

namespace common\models;


use common\models\relations\ScheduleRecordRelations;

class ScheduleRecord extends \common\models\base\ScheduleRecord
{
   use ScheduleRecordRelations;
}