<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%schedule}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $start
 * @property string $end
 * @property string $describe
 * @property string $create_id
 * @property string $venue_id
 * @property string $company_id
 * @property string $create_at
 */
class Schedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%schedule}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['describe'], 'string'],
            [['create_id', 'venue_id', 'company_id', 'create_at'], 'integer'],
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
            'name' => '班次名称',
            'start' => '开始时间',
            'end' => '结束时间',
            'describe' => '班次描述',
            'create_id' => '创建人员 员工ID',
            'venue_id' => '场馆ID',
            'company_id' => '公司ID',
            'create_at' => '创建时间',
        ];
    }
}
