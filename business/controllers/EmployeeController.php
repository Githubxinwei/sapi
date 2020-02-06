<?php
namespace business\controllers;

use common\models\base\Position;
use Yii;
use business\models\Employee;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class EmployeeController extends BaseController
{
    public $modelClass = 'business\models\Employee';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'],$actions['delete']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
        $organization_id = Yii::$app->request->get('organization_id', 0);
        $keyword = Yii::$app->request->get('keyword', 0);
        $query = Employee::find();
        if($organization_id) $query->andWhere(['organization_id'=>$organization_id]);
        if($keyword) $query->andWhere(['or', ['like', 'name', $keyword], ['like', 'mobile', $keyword]]);
        $query->orderBy('id desc');
        return new ActiveDataProvider(['query' => $query]);
    }

    /**
     * @api {get} /business/employees  员工列表
     * @apiName        1员工列表
     * @apiGroup       employee
     * @apiParam  {string}            fields                可选,选择显示字段(例:fields=id,username,mobile)
     * @apiParam  {string}            organization_id       部门ID
     * @apiParam  {string}            keyword               搜索关键词
     * @apiParam  {string}            sort                  排序（[id,sex]，例:sort=-id表示id desc, sort=id表示id asc）
     * @apiParam  {string}            page                  页码（可选，默认1）
     * @apiParam  {string}            per-page              每页显示数（可选，默认20）
     * @apiDescription   员工列表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/02
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/members
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "items": [
                {
                    "id": "802",
                    "name": "申晓",
                    "age": null,
                    "mobile": "15158153285",
                    "organization_id": "84",    //部门ID
                    "position": "普通销售",
                    "status": 1,                //状态：1在职 2离职
                    "pic": "",                  //头像
                    "work_time": 2,             //从业时间
                    "company_id": "75",         //公司ID
                    "venue_id": "76"            //场馆ID
                },
                {
                    "id": "801",
                    "name": "费杰",
                    "age": null,
                    "mobile": "15158153285",
                    "organization_id": "84",
                    "position": "销售经理",
                    "status": 1,
                    "pic": "http://oo0oj2qmr.bkt.clouddn.com/4627441511606417.jpg?e=1511610018&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:A25qVfkkWtXB7jAmtBtIl3lRYyA=",
                    "work_time": null,
                    "company_id": "75",
                    "venue_id": "76"
                },
             ],
            "_links": {
                "self": {
                    "href": "http://127.0.0.3/business/employees?accesstoken=000_1544587932&page=2"
                },
                "first": {
                    "href": "http://127.0.0.3/business/employees?accesstoken=000_1544587932&page=1"
                },
                "prev": {
                    "href": "http://127.0.0.3/business/employees?accesstoken=000_1544587932&page=1"
                },
                "next": {
                    "href": "http://127.0.0.3/business/employees?accesstoken=000_1544587932&page=3"
                },
                "last": {
                    "href": "http://127.0.0.3/business/employees?accesstoken=000_1544587932&page=6"
                }
            },
            "_meta": {
                "totalCount": 110,
                "pageCount": 6,
                "currentPage": 2,
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
     * @api {POST} /business/employee/update?id=1813  修改员工信息
     * @apiName        2修改员工信息
     * @apiGroup       employee
      * @apiParam  {int}               status                在职状态1在职 2离职
      * @apiParam  {int}               work_time             从业年限
      * @apiParam  {string}            position              职务
      * @apiParam  {int}               organization_id       部门ID
     * @apiDescription   修改员工信息
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/02
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/employee/update?id=1813
      * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
             "id": "90605",
             "username": "罗一夫",
             "mobile": "15137123459"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "Object not found: 3",
        "code": 0,
        "status": 404
    }
     */

    /**
     * @api {get} /business/employees/:id  员工信息
     * @apiName        3员工信息
     * @apiGroup       employee
     * @apiDescription   员工信息
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/02
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/members/:id
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "id": "1813",
            "name": "范登科",
            "age": null,
            "sex": 1,
            "mobile": "18739952273",
            "organization_id": "85",
            "position": "wefafea",
            "status": 1,
            "pic": "",
            "work_time": null,
            "company_id": "75",
            "venue_id": "76",
            "company_name": "郑州市艾搏健身服务有限公司",
            "venue_name": "艾搏尊爵汇馆",
            "organization_name": "私教部"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "Object not found: 3",
        "code": 0,
        "status": 404
    }
     */

    /**
     * @api {get} /business/employee/positions  职位列表
     * @apiName        4职位列表
     * @apiGroup       employee
     * @apiParam  {int}               organization_id       部门ID
     * @apiDescription   职位列表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/02
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/employee/positions
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
            "团课总监",
            "团课经理",
            "团课主管",
            "团课教练"
        ]
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "Object not found: 3",
        "code": 0,
        "status": 404
    }
     */
    public function actionPositions($organization_id)
    {
        return ArrayHelper::getColumn(Position::find()->select('name')->where(['department_id'=>$organization_id])->all(), 'name');
    }

}
