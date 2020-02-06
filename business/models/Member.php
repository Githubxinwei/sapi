<?php

namespace business\models;


class Member extends \common\models\Member
{
    public static function find()
    {
        return parent::find()->alias('m')->where(['m.venue_id'=>\Yii::$app->params['authVenueIds']]);
    }

    public function rules()
    {
        return [
            [['username', 'password', 'mobile', 'status', 'member_type', 'venue_id', 'company_id'], 'required'],
            ['username', 'unique'],
            [['register_time', 'update_at', 'last_time', 'last_fail_login_time', 'times', 'status', 'lock_time', 'counselor_id', 'member_type', 'venue_id', 'is_employee', 'company_id'], 'integer'],
            [['params'], 'string'],
            [['username', 'password', 'mobile', 'password_token', 'hash', 'fixPhone'], 'string', 'max' => 200],
        ];
    }

    public function beforeSave($insert)
    {
        if($insert){
            $this->password = \Yii::$app->security->generatePasswordHash($this->password);
            $this->register_time = time();
        }
        return parent::beforeSave($insert);
    }

    public function extraFields()
    {
        return ['memberDetails', 'memberCard'];
    }

    public static function findOne($id)
    {
        return parent::find()->where(['id'=>$id, 'venue_id'=>\Yii::$app->params['authVenueIds']])->one();
    }
}