<?php

namespace service\models;

use business\models\MemberCourseDetail;
use common\models\MemberCourseOrder;

class CoachTeam extends \common\models\Employee
{
    public function fields()
    {
        $field = array();

        $field['id'] = 'id';
        $field['pic'] = 'pic';
        $field['name'] = 'name';
        $field['level'] = 'position';
        $field['mobile'] = 'mobile';

        $listRs = MemberCourseOrder::find()
            ->alias('mco')
            ->joinWith(['member m'], false)
            // ->joinWith(['memberCourseOrder mco' => function($query){
            //     $query->joinWith(['order o' => function($query){}], FALSE);
            // }], FALSE)
            ->select('m.id, mco.product_type, mco.course_type, mco.money_amount')
            ->andWhere(['mco.private_id' => $this->id])
            // ->andWhere(['mco.product_type' => 1, 'mco.pay_status' => 1])
            ->groupBy('m.id')
            ->orderBy('mco.money_amount desc')
            ->asArray()
            ->all();

        $count = count($listRs);
        $deal = 0;
        foreach ($listRs as $v) {
            if (!isset($v['course_type']) || !isset($v['money_amount'])) {
                continue;
            }
            if ((is_null($v['course_type']) || $v['course_type'] == 1) && $v['money_amount'] > 0) {
                $deal += 1;
            }
        }
        $field['succ'] = function() use ($deal) {
            return $deal;
        };
        $field['count'] = function() use ($count) {
            return $count;
        };
        return $field;
    }
}