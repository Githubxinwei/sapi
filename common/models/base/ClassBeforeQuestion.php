<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "cloud_class_before_question".
 *
 * @property string $id 自增ID
 * @property string $title 问题标题
 * @property string $class_id 课程ID
 * @property string $course_id 课种ID
 * @property int $type 1 自定义答案, 2 多选, 3 单选
 * @property string $option 选项
 * @property int $status 状态 0 启用, 1 禁用
 * @property string $create_at 创建时间
 * @property string $update_at 更新时间
 */
class ClassBeforeQuestion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '%class_before_question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['class_id', 'course_id', 'type', 'status', 'create_at', 'update_at'], 'integer'],
            [['option'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'class_id' => 'Class ID',
            'course_id' => 'Course ID',
            'type' => 'Type',
            'option' => 'Option',
            'status' => 'Status',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }
}
