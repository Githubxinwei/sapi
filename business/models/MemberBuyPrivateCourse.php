<?php

namespace business\models;

use common\models\Func;

class MemberBuyPrivateCourse extends \common\models\Member
{
    public static function find()
    {
        // return parent::find()->alias('m')->where(['m.venue_id'=>\Yii::$app->params['authVenueIds']]);
        return parent::find()->alias('m');
    }

    public function fields()
    {
        return [
            'id',

            'name' => function($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'sex' => function($model) {
                $sex = Func::getRelationVal($model, 'memberDetails', 'sex');
                return $sex == 1 ? '男' : ($sex == 2 ? '女' : '未知');
            },

            'pic' => function($model) {
                return Func::getRelationVal($model, 'memberDetails', 'pic');
            },

            // 'level',

            'mobile',

            'time' => function() {
                $time = $this->register_time;
                return $time > 0 ? date('Y-m-d', $time) : '';
            },
        ];
        // $field = array();

        // $field['id'] = 'id';
        // $field['pic'] = 'pic';
        // $field['name'] = 'name';
        // $field['level'] = 'position';
        // $field['mobile'] = 'mobile';


        // $listRs = MemberCourseDetail::find()
        //     ->alias('mco')
        //     ->joinWith('order o', false)
        //     ->select('mco.id, mco.course_type, mco.money_amount')
        //     ->where(['mco.private_id' => $this->id])
        //     ->andWhere(['mco.product_type' => 1, 'mco.pay_status' => 1])
        //     ->asArray()
        //     ->all();
        // $count = count($listRs);
        // $deal = 0;
        // foreach ($listRs as $v) {
        //     if (!isset($v['course_type']) || !isset($v['money_amount'])) {
        //         continue;
        //     }
        //     if (($v['course_type'] == 1 || is_null($v['course_type'])) && $v['money_amount'] > 0) {
        //         $deal += 1;
        //     }
        // }
        // $field['succ'] = function() use ($deal) {
        //     return $deal;
        // };
        // $field['count'] = function() use ($count) {
        //     return $count;
        // };
        // return $field;
    }
}