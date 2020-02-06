<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%schedule_record}}".
 *
 * @property integer $id
 * @property string $schedule_id
 * @property string $coach_id
 * @property string $schedule_date
 * @property string $name
 * @property string $start
 * @property string $end
 * @property string $describe
 * @property string $create_at
 * @property string $create_id
 */
class ScheduleRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%schedule_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['schedule_id', 'coach_id', 'create_at', 'create_id'], 'integer'],
            [['schedule_date'], 'safe'],
            [['describe'], 'string'],
            [['name'], 'string', 'max' => 200],
            [['start', 'end'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'schedule_id' => '排版表ID',
            'coach_id' => '员工私教ID',
            'schedule_date' => '值班时间',
            'name' => '班次名称',
            'start' => '开始时间',
            'end' => '结束时间',
            'describe' => '班次描述',
            'create_at' => '创建时间',
            'create_id' => '创建人员 员工ID',
        ];
    }
}
