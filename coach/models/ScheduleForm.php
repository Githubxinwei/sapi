<?php
namespace coach\models;

use common\models\base\Schedule;
use yii\base\Model;

class ScheduleForm extends Model
{
    public $name;   //班次名称
    public $start;  //开始时间
    public $end;    //结束时间
    public $describe; //描述
    public $companyId; //公司ID
    public $venueId;   //场馆ID
    public $id;
    public function scenarios()
    {
        return [
            'add'  => ['name','start','end','describe','companyId','venueId'],
            'edit' => ['name','start','end','describe','id']
        ];
    }

    /**
     * 班次表规则
     * @return array
     */
    public function rules()
    {
        return [
            ['name', 'required', 'message' => '名称不能为空'],
        ];
    }

    /**
     * 新增班次
     * @return array|bool
     */
    public function insertSave()
    {
        $schedule = new Schedule();
        $schedule->name  = $this->name;
        $schedule->start = $this->start;
        $schedule->end   = $this->end;
        $schedule->company_id = $this->companyId;
        $schedule->describe   = $this->describe;
        $schedule->venue_id   = $this->venueId;
        $schedule->create_at  = time();
        if($schedule->save()){
           return true;
        }else{
           return $schedule->errors;
        }
    }

    /**
     * 修改班次
     * @return array|bool
     */
    public function updateSave()
    {
        $schedule = Schedule::findOne(['id'=>$this->id]);
        if(empty($schedule)){
            return '此班次不存在';
        }
        $schedule->name  = $this->name;
        $schedule->start = $this->start;
        $schedule->end   = $this->end;
        $schedule->describe = $this->describe;
        if($schedule->save()){
            return true;
        }else{
            return $schedule->errors;
        }
    }
}