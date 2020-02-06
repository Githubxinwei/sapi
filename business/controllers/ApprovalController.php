<?php
namespace business\controllers;

use business\models\Approval;
use common\models\ApprovalComment;
use common\models\ApprovalDetails;
use common\models\CardCategory;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;

class ApprovalController extends BaseController
{
    public $modelClass = 'business\models\Approval';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'],$actions['delete']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
        $keyword = Yii::$app->request->get('keyword', 0);
        $type = Yii::$app->request->get('type', 1);
        if(!in_array($type, [1,2,3])) return $this->error('参数type错误');

        $query = Approval::find()->alias('a');
        if($type!=3) $query->joinWith('approvalDetails ad')->andWhere(['ad.approver_id'=>$this->employeeId,'ad.status'=>$type]);

        $arType = $type == 3 ? 2 : 1;
        $query->joinWith(['approvalType at'=>function($q) use($arType){
            $q->joinWith('approvalRole ar')->where(['ar.type'=>$arType, 'ar.employee_id'=>$this->employeeId]);
        }]);

        if($keyword) $query->joinWith('employee e')->andWhere(['or', ['like', 'a.name', $keyword], ['like', 'e.name', $keyword]]);
        $query->orderBy('a.id desc');
        return new ActiveDataProvider(['query' => $query]);
    }

    /**
     * @api {get} /business/approvals  卡种审批列表
     * @apiName        1卡种审批列表
     * @apiGroup       category
     * @apiParam  {string}            type                  1待处理 2已处理 3抄送我
     * @apiParam  {string}            fields                可选,选择显示字段(例:fields=id,type_name,name,create_name,create_at)
     * @apiParam  {string}            keyword               搜索关键词
     * @apiParam  {string}            sort                  排序（[id,sex]，例:sort=-id表示id desc, sort=id表示id asc）
     * @apiParam  {string}            page                  页码（可选，默认1）
     * @apiParam  {string}            per-page              每页显示数（可选，默认20）
     * @apiDescription   卡种审批列表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/17
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/approvals
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "items": [
                {
                    "id": "3",
                    "name": "asdf123",
                    "polymorphic_id": "1557",
                    "status": 3,
                    "number": "151615900070275",
                    "create_at": "1516159000",
                    "create_name": "张丽",
                    "create_pic": "",
                    "create_venue": "艾搏尊爵汇馆",
                    "create_organization": "会务部",
                    "create_position": "",
                    "type_name": "新增会员卡",
                },
                {
                    "id": "2",
                    "name": "asdf123",
                    "polymorphic_id": "1556",
                    "status": 1,
                    "number": "1516152048802454",
                    "create_at": "1516152048",
                    "create_name": "幸福里大上海测试赵冉",
                    "create_pic": "",
                    "create_venue": "艾搏尊爵汇馆",
                    "create_organization": "会务部",
                    "create_position": "",
                    "type_name": "新增会员卡",
                }
            ],
            "_links": {
                "self": {
                    "href": "http://127.0.0.3/business/approvals?accesstoken=001_1544587932&type=3&page=1"
                }
            },
            "_meta": {
                "totalCount": 3,
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
     * @api {get} /business/approvals/:id  卡种审批详情
     * @apiName        2卡种审批详情
     * @apiGroup       category
     * @apiDescription   卡种审批详情
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/17
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/approvals/:id
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "id": "3",
            "name": "asdf123",//卡名称
            "polymorphic_id": "1557",//多态ID，卡种审批即为卡种ID
            "status": 3,//状态:1审批中，2已通过
            "number": "151615900070275",//审批编号
            "create_at": "1516159000",//申请时间
            "create_name": "张丽",//发起人姓名
            "create_pic": "",//发起人头像
            "create_venue": "艾搏尊爵汇馆",//发起人场馆
            "create_organization": "会务部",//发起人部门
            "create_position": "",//发起人职位
            "type_name": "新增会员卡",//审批类型
            "details": [//审批流程
                {
                    "status": 3,//状态：1.审批中,2.已同意，3已拒绝，4已撤销
                    "describe": "就是拒绝",//审批描述
                    "create_at": "1516159000",//申请时间
                    "update_at": "1516159000",//审批时间
                    "employee_name": "幸福里大上海测试赵冉",//审批人姓名
                    "employee_pic": ""//审批人头像
                },
                {
                    "status": 1,
                    "describe": null,
                    "create_at": "1516159000",
                    "update_at": "1516159000",
                    "employee_name": "张丽",
                    "employee_pic": ""
                }
            ],
            "cc": [//抄送人
                "王芳丽"
            ]
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
     * @api {POST} /business/approval/details  同意或拒绝审批
     * @apiName        2同意或拒绝审批
     * @apiGroup       category
     * @apiParam  {int}                id                    审批ID
      * @apiParam  {int}               status                2同意 3拒绝
      * @apiParam  {int}               describe              审批描述
     * @apiDescription   同意或拒绝审批
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/20
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/approval/details
      * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "审批成功",
        "code": 1,
        "status": 200,
        "data": {
            "message": "审批成功"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "Object not found: 3",
        "code": 0,
        "status": 404
    }
     */
    public function actionDetails()
    {
        $id = Yii::$app->request->post('id', 0);
        $status = Yii::$app->request->post('status', 0);
        $describe = Yii::$app->request->post('describe', '');
        if(!in_array($status, [2,3])) $this->error('status参数错误');
        $model = ApprovalDetails::findOne(['approval_id'=>$id, 'approver_id'=>$this->employeeId]);
        if(!$model) return $this->error('审批不存在');
        if($model->status > 1) $this->error('已审批过了');

        $transaction = Yii::$app->db->beginTransaction();
        try{
            $model->status = $status;
            $model->describe = $describe;
            $model->update_at = time();
            if(!$model->save()) throw new Exception(json_encode($model->errors));

            $approval = \common\models\Approval::findOne($id);
            $approval->progress++;

            if($approval->progress == $approval->total_progress){
                $card = CardCategory::findOne($approval->polymorphic_id);
                $card->status = $status == 2 ? 1 : 5;
                if(!$card->save()) throw new Exception(json_encode($card->errors));

                $approval->status = $status;
            }
            if(!$approval->save()) throw new Exception(json_encode($approval->errors));
            $transaction->commit();
        }catch (\Exception $e){
            $transaction->rollBack();
            return $this->error($e->getMessage());
        }

        return $this->success('审批成功');
    }

    /**
     * @api {POST} /business/approval/comment  评论审批
     * @apiName        2评论审批
     * @apiGroup       category
     * @apiParam  {int}                id                    审批ID
      * @apiParam  {int}               content               评论内容
     * @apiDescription   评论审批
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/22
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/approval/comment
      * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "评论成功",
        "code": 1,
        "status": 200,
        "data": {
            "message": "评论成功"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "Object not found: 3",
        "code": 0,
        "status": 404
    }
     */
    public function actionComment()
    {
        $id = Yii::$app->request->post('id', 0);
        $content = Yii::$app->request->post('content', 0);
        if(empty($content)) return $this->error('评论内容不能为空');
        $detail = ApprovalDetails::findOne(['approval_id'=>$id, 'approver_id'=>$this->employeeId]);
        if(!$detail) return $this->error('审批不存在');

        $model = new ApprovalComment();
        $model->approval_detail_id = $detail->id;
        $model->content = $content;
        $model->reviewer_id = $this->employeeId;
        $model->create_at = time();
        return $model->save() ? $this->success('评论成功') : $this->modelError($model->errors);
    }

}
