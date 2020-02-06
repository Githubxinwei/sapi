<?php

namespace group\controllers;

use group\models\Employee;

class UserController extends BaseController
{
    /**
     * @api {get} /group/user/my  我的资料
     * @apiName        我的资料
     * @apiGroup       user
      * @apiParam  {string}            fields                选择显示字段(默认全部，例:fields=id,name,pic)
     * @apiDescription   我的资料
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/05
     * @apiSampleRequest  http://apiqa.aixingfu.net/group/user/my
      * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "id": "266",
            "name": "王杰",
            "age": null,
            "sex": 1,
            "mobile": "15136454905",
            "email": null,
            "birth_time": null,
            "organization_id": "5",
            "position": "团课经理",
            "pic": "http://oo0oj2qmr.bkt.clouddn.com/6285431511420540.png?e=1511424140&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:eJ0D1gLqBpntnoXa_-C8zOz4sS4=",
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "Object not found: 3",
        "code": 0,
        "status": 404
    }
     */
    public function actionMy()
    {
        return Employee::findOne($this->employeeId);
    }

}