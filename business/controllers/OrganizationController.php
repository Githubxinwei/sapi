<?php
namespace business\controllers;

use business\models\Organization;
use common\models\Employee;
use Yii;
use yii\data\ActiveDataProvider;

class OrganizationController extends BaseController
{
    public $modelClass = 'business\models\Organization';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'],$actions['delete'],$actions['update']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
        $pid = Yii::$app->request->get('pid', 0);
        $query = Organization::find()->where(['pid'=>$pid]);
        if($this->user->level){
            if($pid == 0){
                $query->andWhere(['id'=>Yii::$app->params['authCompanyIds']]);
            }else{
                $parent = Organization::findOne($pid);
                if($parent->style == 1){
                    $query->andWhere(['id'=>Yii::$app->params['authVenueIds']]);
                }
            }
        }
        $query->orderBy('id desc');
        $provider = new ActiveDataProvider(['query' => $query]);
        return $provider;
    }

    /**
     * @api {get} /business/organizations  组织列表(公司/场馆/部门)
     * @apiName        1会员列表
     * @apiGroup       organization
     * @apiParam  {string}            fields                可选,选择显示字段(例:fields=id,name)
     * @apiParam  {string}            pid                   上级ID
     * @apiDescription   组织列表(公司/场馆/部门)
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/02
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/organizations
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "items": [
                {
                    "id": "59",
                    "name": "花园路丹尼斯店（尊爵汇）"
                },
                {
                    "id": "33",
                    "name": "管理公司"
                },
                {
                    "id": "15",
                    "name": "亚星游泳健身馆"
                },
            ],
            "_links": {
                "self": {
                    "href": "http://127.0.0.3/business/organizations?pid=1&accesstoken=000_1544587932&fields=id%2Cname&page=1"
                }
            },
            "_meta": {
                "totalCount": 9,
                "pageCount": 1,
                "currentPage": 1,
                "perPage": 20
            }
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "",
        "code": 0,
        "status": 422,
        "data": []
    }
     */

    /**
     * @api {get} /business/organization/employee-count  公司/场馆员工数
     * @apiName        3公司/场馆员工数
     * @apiGroup       organization
     * @apiParam  {string}            id              组织ID
     * @apiDescription   公司/场馆员工数
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/organization/employee-count
         * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": "200"   //员工数
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "",
        "code": 0,
        "status": 422,
        "data": []
    }
     */
    public function actionEmployeeCount($id=0)
    {
        $query = Employee::find()->where(['status'=>1]);
        if($id){
            $model = Organization::findOne($id);
            if(!$model) return $this->error('组织不存在');
            $fields = ['', 'company_id', 'venue_id', 'organization_id'];
            $field = $fields[$model->style];
            $query->andWhere([$field=>$id]);
        }

        return $query->count();
    }

    /**
     * @api {get} /business/organization/venues  所有场馆列表(权限内的)
     * @apiName        3所有场馆列表(权限内的)
     * @apiGroup       organization
     * @apiDescription   所有场馆列表(权限内的)
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/22
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/organization/venues
      * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
            {
                "id": "76",
                "name": "艾搏尊爵汇馆",
                "style": 2
            }
        ]
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "Object not found: 3",
        "code": 0,
        "status": 404
    }
     */
    public function actionVenues()
    {
        return Organization::find()->where(['id'=>Yii::$app->params['authVenueIds']])->all();
    }

}
