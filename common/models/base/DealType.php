<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "{{%deal_type}}".
 *
 * @property string $id
 * @property string $create_id
 * @property string $create_at
 * @property string $type_name
 * @property string $company_id
 * @property string $venue_id
 */
class DealType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%deal_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_id', 'create_at', 'company_id', 'venue_id'], 'integer'],
            [['type_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'create_id' => '创建人id',
            'create_at' => '创建时间',
            'type_name' => '合同类型名字',
            'company_id' => '公司id',
            'venue_id' => '场馆id',
        ];
    }
}
