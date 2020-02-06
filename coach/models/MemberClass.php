<?php
/**
 * 会员带上课信息
 */

namespace coach\models;

use Yii;
use common\models\Func;

class MemberClass extends \common\models\Member
{
    public function fields()
    {
        return [
            'id',

            'pic' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'pic');
            },

            'name' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'name');
            },

            'sex' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'sex') == 1 ? '男' : '女';
            },

            'age' => function ($model) {
                return Func::getRelationVal($model, 'memberDetails', 'age');
            },

            //上课信息
            'class_info' => function ($model) {
                return AboutClassIndex::find()->where(['member_id'=>$model->id, 'coach_id'=>Yii::$app->params['coachId']])->orderBy('id desc')->one();
            },
        ];
    }
}