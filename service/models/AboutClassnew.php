<?php

namespace service\models;

use common\models\AboutClass;
use Yii;
use common\models\Func;

class AboutClassnew extends \common\models\AboutClass
{
    public function fields()
    {
        $obj = AboutClass::find()->select('class_date,coach_id')->where(['id' => $this->id])->asArray()->one();
        !isset($obj['class_date']) && $obj['class_date'] = time();
       $create_at = $obj['class_date'];
//        $end_time = strtotime('+1 day', strtotime($create_at));
        $listRs = AboutClassnew::find()
            ->select('ac.class_date,ac.id,(mco.money_amount/mco.course_amount) as unit_price,ac.class_id,mco.member_id,ac.status,md.name,ac.start,ac.end,mco.overage_section,mco.course_amount,mco.course_amount,mco.product_name,mcod.sale_price,mc.card_number,md.pic,ac.create_at')
            ->alias('ac')
            ->joinWith(['memberCourseOrderDetails mcod'=>function($q){//会员购买私课订单详情表
                $q->joinWith('memberCourseOrder mco',FALSE);//会员课程订单表
            }],FALSE)->joinWith('memberDetails md',FALSE)//会员详细信息表
            ->joinWith('memberCard mc',FALSE)
            ->where(['ac.coach_id'=> $obj['coach_id'],'ac.type'=>1])
            ->andWhere(['ac.status'=>[1,3]])
            ->andWhere(['class_date'=>$create_at])
	        ->orderBy('start ASC')
            ->asArray()->all();
        $field = array();
        $field['dates'] = function() use ($create_at) {
//            return $listRs;
            $now = date('Y-m-d');
            $return = '';
            if ($create_at == $now) {
                $return = '今日';
            } else {
                $return = date('Y-m-d', strtotime($create_at));
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


    }


}