<?php

namespace business\models;


use yii\data\ActiveDataProvider;

class MemberSearch extends Member
{
    public $sex;
    public $keyword;
    public $document_type;

    public function rules()
    {
        return [
            [['venue_id', 'sex', 'status', 'document_type'], 'integer'],
            ['keyword', 'string'],
        ];
    }

    public function search($params)
    {
        $query = Member::find()->alias('m');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['id'=>SORT_DESC],
            'attributes' => ['id','sex'],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'venue_id' => $this->venue_id,
            'status'   => $this->status,
        ]);

        if($this->sex) $query->joinWith('memberDetails md')->andWhere(['md.sex'=>$this->sex]);

        if($this->document_type) $query->joinWith('memberDetails md')->andWhere(['md.document_type'=>$this->document_type]);

        if($this->keyword) $query->joinWith('memberDetails md')->joinWith('memberCard mc')->andWhere(['or',['like', 'md.name', $this->keyword], ['like', 'm.mobile', $this->keyword], ['like', 'mc.card_number', $this->keyword]]);

        return $dataProvider;
    }
}