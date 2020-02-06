<?php

namespace common\models;

use common\models\relations\OrganizationRelations;

class Organization extends \common\models\base\Organization
{
    use OrganizationRelations;
    /**
     * 组织架构 - 关联组织结构表
     * @author houkaixin <houkaixin@itsports.club>
     * @create 2017/7/12
     * @return \yii\db\ActiveQuery
     */
    public function getCompany(){
        return $this->hasOne(Organization::className(),['id'=>'pid']);
    }

    /**
     * 该组织下员工数量
     */
    public function getEmployee_count()
    {
        $fields = ['', 'company_id', 'venue_id', 'organization_id'];
        $field = $fields[$this->style];
        return Employee::find()->where([$field => $this->id, 'status'=>1])->count();
    }
}