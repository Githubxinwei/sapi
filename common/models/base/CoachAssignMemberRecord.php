<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%coach_assign_member_record}}".
 *
 * @property string $id
 * @property integer $coach_id
 * @property integer $member_id
 * @property integer $create_at
 * @property integer $create_id
 * @property integer $is_read
 */
class CoachAssignMemberRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%coach_assign_member_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['coach_id', 'member_id', 'create_id'], 'required'],
            [['coach_id', 'member_id', 'create_at', 'create_id', 'is_read'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键自增',
            'coach_id' => '教练ID',
            'member_id' => '会员ID',
            'created_at' => '创建时间',
            'create_id' => '创建人ID',
            'is_read' => '是否已读',
        ];
    }
}
