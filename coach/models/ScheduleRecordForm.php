<?php
namespace coach\models;

use common\models\base\Schedule;
use common\models\base\ScheduleRecord;
use yii\base\Model;

class ScheduleRecordForm extends Model
{
    public $coachId;  //教练ID
    public $date;     //日期
    public $scheduleId; //班次ID
    public $createId;
    public function rules()
    {
        return [
            ['coachId', 'required', 'message' => '请选择教练'],
            ['date', 'required', 'message' => '请选择日期'],
            ['scheduleId', 'required', 'message' => '请选择班次'],
        ];
    }

    public function save()
    {
        $sr = new ScheduleRecord();
        $schedule = Schedule::findOne(['id'=>$this->scheduleId]);
        $sr->coach_id      = $this->coachId;
        $sr->schedule_date = $this->date;
        $sr->schedule_id   = $this->scheduleId;
        $sr->name          = $schedule->name;
        $sr->describe      = $schedule->describe;
        $sr->start         = $schedule->start;
        $sr->end           = $schedule->end;
        $sr->create_at     = time();
        $sr->create_id     = $this->createId;

        if($sr->save()){
            return true;
        }
        return $sr->errors;
    }
}