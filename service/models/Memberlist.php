<?php

namespace service\models;

use common\models\base\Admin;
use common\models\Func;
use common\models\relations\MemberRelations;
use common\models\MemberCard;
use common\models\MemberResult;
use common\models\Employee;
use common\models\FollowMember;
class Memberlist extends \common\models\base\Member
{
    use MemberRelations;
    public function fields()
    {
        return [
            'member_id'=> 'id',
            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'sex' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'sex') == 1 ? '男' : '女';
            },
            'mobile',
            'card_number' => function($model){
                return MemberCard::findOne(['member_id'=>$model->id])->card_number;
            },
            'experience' => function($model)
            {
                $var = MemberResult::find()
                            ->where(['member_id'=>$model->id])
                            ->asArray()
                            ->one();
                if(!empty($var))
                {
                    $data['complete'] = MemberResult::findOne(['member_id'=>$model->id])->complete.'%';
                    $data['calorie'] = MemberResult::findOne(['member_id'=>$model->id])->calorie.'cal';
                    if(MemberResult::findOne(['member_id'=>$model->id])->motion == 1)
                    {
                        $data['motion'] = '很用力';
                    }
                    if(MemberResult::findOne(['member_id'=>$model->id])->motion == 2)
                    {
                        $data['motion'] = '中等';
                    }
                    if(MemberResult::findOne(['member_id'=>$model->id])->motion == 3)
                    {
                        $data['motion'] = '不太用力';
                    }
                    $data['motion_qd'] = MemberResult::findOne(['member_id'=>$model->id])->motion_qd;
                }else{
                    $data['complete'] = "暂无";
                    $data['calorie'] = "暂无";
                    $data['motion'] = "暂无";
                    $data['motion_qd'] = "暂无";
                }

                return $data;
            },
            'followmember' => function($model)
            {
                $data = FollowMember::find()
                        ->alias('fa')
                        ->select('fa.id,fa.content,fw.title,fa.create_at,fw.id as follow_id')
                        ->joinWith('followWay fw',FALSE)
                        ->where(['fa.member_id'=>$model->id])
//                        ->andWhere(['operation'=>6])
                        ->orderBy('fa.create_at desc')
                        ->asArray()
                        ->one();
                $obj = FollowMember::find()
                    ->alias('fa')
                    ->select('fa.id,fa.content,fw.title,fa.create_at')
                    ->joinWith('followWay fw',FALSE)
                    ->where(['fa.member_id'=>$model->id])
//                    ->andWhere(['operation'=>6])
                    ->orderBy('fa.create_at desc')
                    ->asArray()
                    ->all();
                $data1['sum'] = count($obj);
                return array_merge($data1,$data);
            },
            'operation' => function($model)
            {
                return "现在没数据";
            }
        ];
    }
}