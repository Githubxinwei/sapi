<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%information_records}}".
 *
 * @property string $id
 * @property string $member_id
 * @property string $member_card_id
 * @property string $create_at
 * @property string $note
 * @property integer $behavior
 * @property integer $create_id
 */
class InformationRecords extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%information_records}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'behavior'], 'required'],
            [['member_id', 'member_card_id', 'create_at', 'behavior', 'create_id'], 'integer'],
            [['note'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'member_id' => '会员ID',
            'member_card_id' => '会员卡ID',
            'create_at' => '创建时间',
            'note' => '备注',
            'behavior' => '行为：1延期开卡，2解冻，3冻结',
            'create_id' => '创建人id',
        ];
    }
}
