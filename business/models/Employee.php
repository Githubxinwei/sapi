<?php

namespace business\models;

use common\models\Func;
use Yii;
use yii\web\ForbiddenHttpException;

class Employee extends \common\models\Employee
{
    public static function find()
    {
        return parent::find()->alias('e')->where(['e.venue_id'=>\Yii::$app->params['authVenueIds']]);
    }

    public function fields()
    {
        return [
            'id',

            'name',

            'age',

            'sex',

            'mobile',

            'organization_id',

            'position',

            'status',

            'pic',

            'work_time',

            'company_id',

            'venue_id',

            'signature',

            'company_name' => function ($model) {
                return Func::getRelationVal($model, 'company', 'name');
            },

            'venue_name' => function ($model) {
                return Func::getRelationVal($model, 'venue', 'name');
            },

            'organization_name' => function ($model) {
                return Func::getRelationVal($model, 'organization', 'name');
            },
        ];
    }

    public function rules()
    {
        return [
            [['status', 'work_time', 'organization_id', 'updated_at'], 'integer'],
            [['position'], 'string', 'max' => 200],
            ['organization_id', 'validateOrganization'],
        ];
    }

    public function validateOrganization($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $had = Organization::find()->where(['id'=>$this->organization_id, 'pid'=>Yii::$app->params['authVenueIds']])->one();
            if (!$had) {
                $this->addError($attribute, '部门ID不存在.');
            }
        }
    }

    public function extraFields()
    {
        return ['organization', 'venue', 'company'];
    }

    public static function findOne($id)
    {
        return parent::find()->where(['id'=>$id, 'venue_id'=>\Yii::$app->params['authVenueIds']])->one();
    }
}