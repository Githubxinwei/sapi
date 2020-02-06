<?php

namespace service\models;

use common\models\Func;
use common\models\MemberDetails;
use yii\base\Model;
use common\models\MemberCourseOrder;
use common\models\GroupClass;

class EvaluateView extends \common\models\Evaluate
{
    public function fields()
    {
        $fields = array();

        $fields['id'] = 'id';

        $result = array();
        if ($this->consumption_type == 'teamClass') {
            $result = GroupClass::find()
                ->alias('gc')
                ->joinWith('course c', false)
                ->select('c.name, c.pic,gc.coach_id,gc.course_id,c.course_desrc,gc.start,gc.end')
                ->where(['gc.id' => $this->consumption_type_id])
                ->asArray()
                ->one();
        } elseif ($this->consumption_type == 'privateClass') {
            $result = MemberCourseOrder::find()
                ->alias('mco')
                ->joinWith('chargeClass cc', false)
                ->select('cc.name, cc.pic')
                ->where(['mco.id' => $this->consumption_type_id])
                ->asArray()
                ->one();
        } else {
            $result['name'] = '';
            $result['pic'] = '';
        }

        $fields['pic'] = function () use ($result) {
            return $result['pic'];
        };

        $fields['name'] = function () use ($result) {
            return $result['name'];
        };

        $fields['type'] = function($model) {
            $type = '';
            switch ($model->consumption_type) {
                case 'smallClass':
                    $type = '小团体课';
                    break;
                case 'privateClass':
                    $type = '私教课';
                    break;
                default:
                    $type = '团教课';
                    break;
            }
            return $type;
        };

        $fields['level'] = 'star_level';

        $fields['content'] = 'content';

        $fields['time'] = function($model) {
            return date('Y-m-d H:i', $model->create_at);
        };
        $fields['enclosure'] = function($model) {
            return $model->enclosure;
        };
        $fields['label_id'] = function($model) {
            return $model->label_id;
        };
        $fields['coach_name'] = function () use ($result) {
            return array_column(\common\models\base\Employee::find()->where(['id'=>$result['coach_id']])->all(),'name')[0];
        };
        $fields['course_id'] = function () use ($result) {
            return $result['course_id'];
        };
        $fields['time'] = function($model) {
            return date('Y-m-d H:i', $model->create_at);
        };
        $fields['memberName'] = function($model) {
            return $model->display_status==1 ? '匿名': array_column(MemberDetails::find()->where(['member_id'=>$model->member_id])->all(),'name')[0];
        };
        $fields['time_len'] = function () use ($result) {
            return  ($result['end'] - $result['start'])/60;
        };
        $fields['course_desrc'] = function () use ($result) {
            return  $result['course_desrc'] ;
        };


        return $fields;
    }


}