<?php
namespace business\controllers;

class MemberController extends BaseController
{
    public $modelClass = 'business\models\Member';

    public function actions()
    {
        $actions = parent::actions();
        //$actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
        $searchModel = new \business\models\MemberSearch();
        return $searchModel->search(\Yii::$app->request->queryParams);
    }

    /**
     * @api {get} /business/members  会员列表
     * @apiName        1会员列表
     * @apiGroup       member
     * @apiParam  {string}            fields                选择显示字段(默认全部，例:fields=id,username,mobile)
     * @apiParam  {string}            MemberSearch[field]   筛选字段([venue_id,sex,status,keyword]，例:MemberSearch[venue_id]=9&MemberSearch[sex]=1)
     * @apiParam  {string}            sort                  排序（[id,sex]，例:sort=-id表示id desc, sort=id表示id asc）
     * @apiParam  {string}            page                  页码（可选，默认1）
     * @apiParam  {string}            per-page              每页显示数（可选，默认20）
     * @apiDescription   会员列表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/27
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
                    "id": "90807",
                    "username": "13123245035",
                    "mobile": "13123245035"
                },
                {
                    "id": "90605",
                    "username": "罗一夫",
                    "mobile": "15137123459"
                }
            ],
            "_links": {
                "self": {
                    "href": "http://127.0.0.3/business/members?fields=id%2Cusername%2Cmobile&MemberSearch%5Bsex%5D=1&MemberSearch%5Bkeyword%5D=123&sort=-id&per-page=2&page=1"
                },
                "next": {
                    "href": "http://127.0.0.3/business/members?fields=id%2Cusername%2Cmobile&MemberSearch%5Bsex%5D=1&MemberSearch%5Bkeyword%5D=123&sort=-id&per-page=2&page=2"
                },
                "last": {
                    "href": "http://127.0.0.3/business/members?fields=id%2Cusername%2Cmobile&MemberSearch%5Bsex%5D=1&MemberSearch%5Bkeyword%5D=123&sort=-id&per-page=2&page=278"
                }
            },
            "_meta": {
                "totalCount": 556,
                "pageCount": 278,
                "currentPage": 1,
                "perPage": 2
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
     * @api {get} /system/members/:id  会员详情
     * @apiName        5会员详情
     * @apiGroup       member
      * @apiParam  {string}            fields                选择显示字段(默认全部，例:fields=id,username,mobile)
     * @apiDescription   会员详情
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/25
     * @apiSampleRequest  http://apiqa.aixingfu.net/system/members/:id
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

}
