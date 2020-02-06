<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "cloud_follow_member".
 *
 * @property string $id 自增ID
 * @property string $content 跟进内容
 * @property string $follow_way_id 私教跟进方式ID
 * @property string $actual_time 实际跟进时间
 * @property string $create_at 添加时间
 * @property string $next_time 下次跟进时间
 * @property int $is_delete 软删标记
 * @property string $associates 关联人姓名
 * @property string $associates_id 关联人ID
 * @property string $remind_id 提醒ID
 * @property string $member_id 会员ID
 * @property string $coach_id 教练ID
 */
class FollowMember extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cloud_follow_member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['follow_way_id', 'is_delete', 'associates_id', 'remind_id', 'member_id', 'coach_id'], 'integer'],
            [['actual_time', 'create_at', 'next_time'], 'safe'],
            [['content'], 'string', 'max' => 200],
            [['associates'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'content' => '跟进内容',
            'follow_way_id' => '私教跟进方式ID',
            'actual_time' => '实际跟进时间',
            'create_at' => '添加时间',
            'next_time' => '下次跟进时间',
            'is_delete' => '软删标记',
            'associates' => '关联人姓名',
            'associates_id' => '关联人ID',
            'remind_id' => '提醒ID',
            'member_id' => '会员ID',
            'coach_id' => '教练ID',
        ];
    }
}
