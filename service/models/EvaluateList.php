<?php

namespace service\models;

use common\models\Func;
use common\models\MemberDetails;
use yii\base\Model;
use common\models\MemberCourseOrder;
use common\models\GroupClass;

class EvaluateList extends \common\models\Evaluate
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
                ->select('c.name, c.pic')
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
        $fields['venue_name'] = function($model) {
            return Func::getRelationVal($model,'organization','name');
        };
        $fields['memberName'] = function($model) {
            return $model->display_status==1 ? '匿名': array_column(MemberDetails::find()->where(['member_id'=>$model->member_id])->all(),'name')[0];
        };
        return $fields;
    }

    public static function getCourseDetail($course_id, $course_type, $isMore = false)
    {
        if (!in_array($course_type, ['teamClass', 'privateClass', 'smallClass'])) {
            return [];
        }

        if ($course_type == 'teamClass') {
            $result = GroupClass::find()
                ->alias('gc')
                ->joinWith('course c', false)
                ->select('c.name, c.pic')
                ->where(['gc.id' => $course_id])
                ->asArray()
                ->one();
        } elseif ($course_type == 'privateClass') {
            $result = MemberCourseOrder::find()
                ->alias('mco')
                ->joinWith('chargeClass cc', false)
                ->select('cc.name, cc.pic')
                ->where(['mco.id' => $course_id])
                ->asArray()
                ->one();
        } else {

        }
        return $result;
    }
}