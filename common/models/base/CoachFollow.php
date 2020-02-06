<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "cloud_coach_follow".
 *
 * @property string $id 自增ID
 * @property string $coach_id 教练ID
 * @property string $member_id 会员ID
 * @property int $category 跟进方式: 1 QQ; 2 微信; 3 电话
 * @property string $create_at 创建时间
 * @property string $content 跟进内容
 */
class CoachFollow extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cloud_coach_follow';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['coach_id', 'member_id', 'category', 'create_at'], 'integer'],
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'coach_id' => '教练ID',
            'member_id' => '会员ID',
            'category' => '跟进方式: 1 QQ; 2 微信; 3 电话',
            'create_at' => '创建时间',
            'content' => '跟进内容',
        ];
    }
}
