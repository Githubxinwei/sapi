<?php
namespace coach\models;

use common\models\Func;

class ScheduleRecord extends \common\models\ScheduleRecord
{
    public $date_start;
    public $date_end;
     public function fields()
     {
         $start = \Yii::$app->request->get('date_start');
         $end = \Yii::$app->request->get('date_end');
         return [
             'coach' => function($model){
                 return Func::getRelationVal($model, 'employee', 'name');
             },
             'schedule_record' => function($model)use($start,$end)
             {
                   return $this->getScheduleRecordData($model,$start,$end);
             }
         ];
     }

    public function getScheduleRecordData($model,$start,$end)
    {
        $data =  ScheduleRecord::find()
//            ->where(['between','schedule_date',$model->date_start,$model->date_end])
            ->andWhere(['coach_id'=>$this->coach_id])
            ->asArray()->all();
        $data = $this->setDateRecord($data,$start,$end);
        return $data;
    }
    /**
     * 处理日期数据
     */
    public function setDateRecord($data = null,$start = null,$end = null)
    {
        $arr = [];
        if(empty($data)){
            return $data;
        }
        $stat = intval(date('d',strtotime($start)));
        $end  = intval(date('d',strtotime($end)));
        for ($i = $stat; $i <= $end; $i++){
              $arr['V'.$i] = null;
        }
        foreach ($data as &$v){
            $i = intval(date('d',strtotime($v['schedule_date'])));
            $arr['V'.$i] = $v;
        }
        return $arr;
    }
}