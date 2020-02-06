<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/17
 * Time: 上午 09:38
 */

namespace common\models\base;

use Yii;

class Evaluate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evaluate}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'consumption_type_id', 'consumption_type'], 'required'],
            [['member_id', 'consumption_type_id', 'display_status', 'venue_id', 'company_id', 'create_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键自增',
            'member_id' => '会员ID',
            'consumption_type_id' => '团课ID,私课ID,小团体课ID',
            'consumption_type' => '标识',
            'display_status' => '匿名 : 1 ,非匿名: 2',
            'venue_id' => '场馆ID',
            'company_id' => '公司ID',
            'content' => '会员评价内容',
            'star_level' => '星评: 最高五颗星',
            'create_at' => '创建时间',
            'enclosure' => '图片,文件',
            'label_id' => '标签ID',
        ];
    }
}