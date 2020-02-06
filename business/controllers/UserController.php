<?php
namespace business\controllers;

use Yii;
use business\models\Employee;
use business\models\UserUpdateForm;
use common\models\Advice;
use yii\web\UploadedFile;

class UserController extends BaseController
{
        /**
     * @api {get} /business/user/info  我的资料
     * @apiName        1我的资料
     * @apiGroup       user
     * @apiDescription   我的资料
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/17
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/user/info
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
            "signature":"sdfsdf",//签名
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
    public function actionInfo()
    {
        return Employee::findOne($this->employeeId);
    }

     /**
     * @api {POST} /business/user/update  修改我的资料
     * @apiName        2修改我的资料
     * @apiGroup       user
      * @apiParam  {string}               signature       签名
      * @apiParam  {string}               sex             性别1男2女
      * @apiParam  {string}               name            姓名
      * @apiParam  {string}               mobile          手机号码
      * @apiParam  {string}               code            验证码
      * @apiParam  {string}               newpwd          新密码
      * @apiParam  {file}                 pic             头像图片
     * @apiDescription   修改我的资料
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/17
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/user/update
      * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "修改成功",
        "code": 1,
        "status": 200,
        "data": {
            "message": "修改成功"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "",
        "code": 0,
        "status": 422,
        "data": [
            {
                "field": "code",
                "message": "验证码错误"
            }
        ]
    }
     */
    public function actionUpdate()
    {
        $model = new UserUpdateForm(['user_id'=>$this->userId, 'employee_id'=>$this->employeeId]);
        $model->load($this->post, '');
        if(isset($_FILES['pic'])) $model->pic = UploadedFile::getInstanceByName('pic');
        return $model->update() ? $this->success('修改成功') : $this->modelError($model->errors);
    }

    /**
     * @api {POST} /business/user/advice  意见反馈
     * @apiName        3意见反馈
     * @apiGroup       user
      * @apiParam  {string}               content       反馈内容
     * @apiDescription   意见反馈
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/22
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/user/advice
      * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "修改成功",
        "code": 1,
        "status": 200,
        "data": {
            "message": "修改成功"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "",
        "code": 0,
        "status": 422,
        "data": [
            {
                "field": "content",
                "message": "反馈内容不能为空"
            }
        ]
    }
     */
    public function actionAdvice()
    {
        $content = Yii::$app->request->post('content', '');
        if(empty($content)) return $this->error('反馈内容不能为空');
        $model = new Advice();
        $model->admin_id = $this->userId;
        $model->content = $content;
        $model->create_at = time();
        return $model->save() ? $this->success('反馈成功') : $this->modelError($model->errors);
    }
}
