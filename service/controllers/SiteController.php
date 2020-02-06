<?php
namespace service\controllers;

use common\models\Version;
use service\models\FindPwdForm;
use service\models\LoginForm;
use service\models\RegisterForm;
use service\base\BaseController;
use Yii;


class SiteController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['*'];
        return $behaviors;
    }
    /**
     * @api {post} /service/site/login   登录
     * @apiName        1登录
     * @apiGroup       site
     * @apiParam  {string}        mobile       手机号
     * @apiParam  {string}        password       密码
     * @apiDescription   登录
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/25
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/site/login
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
    "message": "",
    "code": 1,
    "status": 200,
    "data": {
    "accesstoken": "Z1MaRW6N2zmu2FrnebBHhU_zVTHU_Qov", //accesstoken,登录后的操作每次请求都需要在请求header中加上Authorization：Bearer Z1MaRW6N2zmu2FrnebBHhU_zVTHU_Qov,
    "style":"",//本人权限级别（0可管理多个公司，1可管理单个公司，2可管理单个场馆）
    "pid":"1",//本人拥有最高权限组织ID
    "company_name": "我爱运动瑜伽健身",
    "venue_name": "大上海瑜伽健身馆"
    "pic"头像
    "postion" 1私教经理 2私教 3管理
    }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
    "message": "",
    "code": 0,
    "status": 422,
    "data": [
    {
    "field": "password",
    "message": "手机号码或密码错误."
    }
    ]
    }
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load($this->post, '');

        return $model->login() ? $model->info() : $this->modelError($model->errors);
    }


    /**
     * @api {post} /business/site/register   注册
     * @apiName        3注册
     * @apiGroup       site
     * @apiParam  {string}        username     用户名
     * @apiParam  {string}        mobile       手机号码
     * @apiParam  {string}        code         验证码
     * @apiParam  {string}        truename     姓名
     * @apiParam  {string}        company_id   公司ID
     * @apiParam  {string}        venue_id     场馆ID
     * @apiParam  {string}        partment_id  部门ID
     * @apiParam  {string}        password     密码
     * @apiDescription   登录
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/25
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/site/register
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "注册成功",
        "code": 1,
        "status": 200,
        "data": {
            "message": "注册成功"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "",
        "code": 0,
        "status": 422,
        "data": [
            {
                "field": "mobile",
                "message": "手机号码的值\"17796655022\"已经被占用了。"
            }
        ]
    }
     */

    public function actionRegister()
    {
        $model = new RegisterForm();
        $model->load($this->post, '');

        return $model->register() ? $this->success('注册成功') : $this->modelError($model->errors);
    }


    /**
     * @api {post} /service/site/find-pwd   找回密码
     * @apiName        4找回密码
     * @apiGroup       site
     * @apiParam  {string}        mobile         手机号码
     * @apiParam  {string}        code           验证码
     * @apiParam  {string}        newpwd         新密码
     * @apiDescription   找回密码
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/25
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/site/find-pwd
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "accesstoken": "Z1MaRW6N2zmu2FrnebBHhU_zVTHU_Qov",
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
                "message": "验证码错误."
            }
        ]
    }
     */
    public function actionFindPwd()
    {
        $model = new FindPwdForm();
        $model->load($this->post, '');

        return $model->reset() ? $model->info() : $this->modelError($model->errors);
    }

    /**
     * @api {POST} /business/site/version  检查更新
     * @apiName        5检查更新
     * @apiGroup       site
     * @apiParam  {string}                name                    产品名称:ios_或android_前缀+名称,目前名称有<br>[私教端：coach][管理端：business][团教端：group][会员端：customer]<br>例如ios_business表示IOS版管理端
     * @apiDescription   检查更新
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/20
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/site/version
      * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "id": 1,
            "name": "ios_business",
            "version": "1.0",//最新版本号
            "url": "",//安装文件url
            "must": 0//0不必须更新，1必须更新
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "Object not found: 3",
        "code": 0,
        "status": 404
    }
     */
    public function actionVersion(){
        // $push = Yii::$app->push
        //     ->setTitle('标题')
        //     ->setBody('消息正文')
        //     ->setExtendedData(['a' => 1, 'b' => 2]); // 自定义扩展参数;
        // $push->sendAll('26539c2603083d532bfe85950195211');
        // return 12;
        
        $name = \Yii::$app->request->get('name',0);
        $version = \Yii::$app->request->get('version',0);
        if (!$name) return $this->error('参数异常!');
        $version_version =  Version::find()->where(['name'=>$name])->orderBy('update_time desc')->one();
        if (!$version) return $this->error('暂无更新!');
        if ($version_version->version > $version ){
            return $version_version;
        }else{
           return $this->error('暂无更新!');
        }



    }

}
