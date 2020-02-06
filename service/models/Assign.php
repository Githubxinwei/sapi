<?php

namespace service\models;

use common\models\Func;

class Assign extends \common\models\CoachAssignMemberRecord
{
    public function fields()
    {
        $create_at = Assign::find()->select('create_at')->where(['id' => $this->id])->asArray()->one();
        !isset($create_at['create_at']) && $create_at['create_at'] = time();
        $create_at = strtotime(date('Y-m', $create_at['create_at']));
        $end_time = strtotime('+1 months', $create_at);

        $listRs = Assign::find()->alias('a')
            ->select('a.id, md.pic, md.name, m.mobile, a.member_id, e.name as coach_name, ma.name as assign_name, a.create_at as assign_time')
            ->joinWith('member m', FALSE)
            ->joinWith('memberDetails md', FALSE)
            ->joinWith('employee e', FALSE)
            ->joinWith('management ma', FALSE)
            ->where(['>', 'a.create_at', $create_at])
            ->andWhere(['<', 'a.create_at', $end_time])
            ->orderBy('id desc')
            ->asArray()
            ->all();
        foreach ($listRs as $k => $v) {
            $listRs[$k]['assign_time'] = date('m-d H:i', $v['assign_time']);
        }
        
        $field = array();
        $field['date'] = function() use ($create_at) {
            $now = strtotime(date('Y-m', time()));
            $return = '';
            if ($create_at == $now) {
                $return = '本月';
            } else {
                $return = date('m', $create_at) . '月';
            }
            return $return;
        };

        $count = count($listRs);
        $field['count'] = function () use ($count) {
            return $count;
        };
        $field['list'] = function() use ($listRs) {
            return $listRs;
        };
        return $field;
        return [
            'id',

            'pic' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'pic');
            },

            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'mobile' => function ($model) {
                return Func::getRelationVal($model, 'member', 'mobile');
            },

            'member_id',

            'coach_name' => function ($model) {
                return Func::getRelationVal($model, 'employee', 'name');
            },

            'assign_name' => function ($model) {
                return Func::getRelationVal($model, 'management', 'name');
            },

            'assign_time' => function ($model) {
                return $model->create_at ? date('m-d H:i:s', $model->create_at) : '';
            },
        ];
    }
}