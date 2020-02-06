<?php
namespace group\controllers;

use common\models\IcBindRecord;
use common\models\AboutClass;
use common\models\AboutYard;
use common\models\Classroom;
use common\models\MemberDetails;
use common\models\Organization;
use group\models\FindPwdForm;
use group\models\LoginCodeForm;
use group\models\LoginForm;
use group\models\RegisterForm;

class SiteController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['*'];
        return $behaviors;
    }

    /**
     * @api {post} /group/site/login   登录
     * @apiName        1登录
     * @apiGroup       site
     * @apiParam  {string}        mobile         手机号码
     * @apiParam  {string}        password       密码
     * @apiDescription   登录
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/01/04
     * @apiSampleRequest  http://apiqa.aixingfu.net/group/site/login
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "accesstoken": "Z1MaRW6N2zmu2FrnebBHhU_zVTHU_Qov", //accesstoken,登录后的操作每次请求都需要在请求header中加上Authorization：Bearer Z1MaRW6N2zmu2FrnebBHhU_zVTHU_Qov
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
        $model = new LoginForm($this->language);
        $model->load($this->post, '');
        return $model->login() ? $model->info() : $this->modelError($model->errors);
    }

    /**
     * @api {post} /group/site/login-code   验证码登录
     * @apiName        2验证码登录
     * @apiGroup       site
     * @apiParam  {string}        mobile         手机号码
     * @apiParam  {string}        code           验证码
     * @apiDescription   验证码登录
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/01/04
     * @apiSampleRequest  http://apiqa.aixingfu.net/group/site/login
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "accesstoken": "Z1MaRW6N2zmu2FrnebBHhU_zVTHU_Qov", //accesstoken,登录后的操作每次请求都需要在请求header中加上Authorization：Bearer Z1MaRW6N2zmu2FrnebBHhU_zVTHU_Qov
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
    public function actionLoginCode()
    {
        $model = new LoginCodeForm($this->language);
        $model->load($this->post, '');
        return $model->login() ? $model->info() : $this->modelError($model->errors);
    }

    /**
     * @api {post} /group/site/find-pwd   找回密码
     * @apiName        4找回密码
     * @apiGroup       site
     * @apiParam  {string}        mobile         手机号码
     * @apiParam  {string}        code           验证码
     * @apiParam  {string}        newpwd         新密码
     * @apiDescription   找回密码
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/01/04
     * @apiSampleRequest  http://apiqa.aixingfu.net/group/site/find-pwd
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
        $model = new FindPwdForm($this->language);
        $model->load($this->post, '');
        return $model->reset() ? $model->info() : $this->modelError($model->errors);
    }

    /**
     * @api {get} /group/site/in   手环上课打卡
     * @apiName        5手环上课打卡
     * @apiGroup       site
     * @apiParam  {string}        ic_number         手环ID
     * @apiParam  {string}        sn                设备ID
     * @apiParam  {string}        readno            读卡器ID
     * @apiDescription   手环上课打卡
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/01/06
     * @apiSampleRequest  http://apiqa.aixingfu.net/group/site/in
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        message: "打卡成功",
        "code": 1,
        "status": 200,
        "data": {
            message: "打卡成功"
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "name": "Not Found",
        "message": "您没有该教室30分钟内要上的课",
        "code": 0,
        "status": 404,
        "type": "yii\\web\\NotFoundHttpException"
    }
     */
    public function actionIn($ic_number,$sn,$readno)
    {
        $IcBind = IcBindRecord::findOne(['ic_number' => $ic_number,'status' => 1]);
        if(empty($IcBind)) return $this->returnError('IC卡不存在');
        $room = Organization::findOne(['code' => $sn,'style' => 4]);
        if(empty($room)) return $this->returnError('房间不存在');
//        $classroomModel = Classroom::findOne(['id'=>$sn]);
//        if(empty($classroomModel)) return $this->returnError('教室不存在');
//        if (!in_array($readno,explode(',',$classroomModel->readno)))return $this->error('教室无此门！');
        $data = [];
        $data['today']     = date('Y-m-d');
        $data['member_id'] = $IcBind->member_id;
//        $data['id']        = $classroomModel->id;
        $data['time']      = time();
        $data['type']      = 'start';
        $model = $this->actionGetClassDetail($data,$room->id);
        if(empty($model)){
            $data['type']    = 'in_class';
            $model = $this->actionGetClassDetail($data,$room->id);
            if($model) return $this->returnError('上课期间，不准外出');
            $data['type']    = 'end';
            $model = $this->actionGetClassDetail($data,$room->id);
            if(!empty($model) && time() > $model->end+15*60){
                return $this->returnError('课程已结束打卡');
            }
            if(!empty($model) && $model->end < time() && time() < $model->end+15*60){
                if($model->in_time === 0){
                    return  $this->returnError('课程未进行上课打卡');
                }
                $model->out_time = time();
//                $model->status   = 4;
                return $model->save() ? $this->success('下课打卡成功') : $this->modelError($model->errors);
            }
            return $this->returnError('对不起，该时间段没有课程');
        }
        if($model->in_time === 0){
            $model->in_time = time();
//            $model->status  = 3;
            return $model->save() ? $this->success('上课打卡成功') : $this->modelError($model->errors);
        }
        if($sn == '223229130-2' || $sn == '223229130-1'){
            return $this->success(true);
        }
        if(time() >= $model->start - 15*60 && time() < $model->start){
            return $this->success(true);
        }
        return $this->returnError('上课期间，不准外出');
    }

    /**
     * @desc   App - 获取预约课程详情
     * @author 李慧恩<lihuien@itsports.club>
     * @create: 2018-03-20
     * @param $data
     * @return array|null|\yii\db\ActiveRecord
     */
    public function actionGetClassDetail($data,$roomId)
    {
        $query = AboutClass::find()->alias('ac')
            ->joinWith(['groupClass gc' => function($query){
                $query->joinWith(['classroom cr']);
            }])
            ->where(['ac.class_date' => $data['today'],
                'ac.member_id' => $data['member_id'],
                'ac.type' => 2,
                'cr.room_id' => $roomId
            ])
            ->andWhere(['<>','ac.status',2]);
        if($data['type'] === 'start'){
            $query->andWhere(['between', 'ac.start', $data['time'], $data['time']+15*60]);
        }elseif($data['type'] === 'in_class'){
            $query->andWhere(['and',['>=','ac.end',$data['time']], ['<=','ac.start',$data['time']]]);
        }else{
            $query->andWhere([ '<=','ac.end', $data['time']]);
            $query->orderBy('id DESC');
        }
        return $query->one();
    }

    /**
     * @api {get} /group/site/in   手环上课打卡
     * @apiName        5手环上课打卡
     * @apiGroup       site
     * @apiParam  {string}        ic_number         手环ID
     * @apiParam  {string}        sn                设备ID
     * @apiParam  {string}        readno            读卡器ID
     * @apiDescription   手环上课打卡
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/01/06
     * @apiSampleRequest  http://apiqa.aixingfu.net/group/site/in
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
    message: "打卡成功",
    "code": 1,
    "status": 200,
    "data": {
    message: "打卡成功"
    }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
    "name": "Not Found",
    "message": "您没有该教室30分钟内要上的课",
    "code": 0,
    "status": 404,
    "type": "yii\\web\\NotFoundHttpException"
    }
     */
    public function actionInYard($ic_number, $sn, $type,$readno)
    {
        $IcBind = IcBindRecord::findOne(['ic_number' => $ic_number,'status' => 1]);
        if(empty($IcBind)) return $this->returnError('IC卡不存在');
        $room = Organization::findOne(['code' => $sn,'style' => 4]);
        if(empty($room)) return $this->returnError('房间不存在');
//        $classroomModel = VenueYard::findOne(['id'=>$sn]);
//        if(!$classroomModel) return $this->returnError('教室不存在');
//        if (!in_array($readno,explode(',',$classroomModel->readno)))return $this->error('教室无此门！');
        $data = [];
        $data['today']     = date('Y-m-d');
        $data['member_id'] = $IcBind->member_id;
//        $data['id']        = $classroomModel->id;
        $data['time']      = time();
        $data['type']      = 'start';
        $model = $this->actionGetClassYardDetail($data,$room->id);
        if(empty($model)){
            $data['type']    = 'in_class';
            $model = $this->actionGetClassYardDetail($data,$room->id);
            if($model) $this->success(true);;
            $data['type']    = 'end';
            $model = $this->actionGetClassYardDetail($data,$room->id);
            if(empty($model) && time() > $model->end+15*60){
                return $this->returnError('您的课程已结束打卡');
            }
            if(empty($model) && $model->end < time() && time() < $model->end+15*60){
                if(empty($model->in_time)){
                    return  $this->returnError('您的课程未进行上课打卡');
                }
                $model->out_time = time();
                return $model->save() ? $this->success('下课打卡成功') : $this->modelError($model->errors);
            }
            return $this->returnError('对不起 该时间段没有课程');
        }
        if(!$model->in_time){
            $model->in_time = time();
            return $model->save() ? $this->success('上课打卡成功') : $this->modelError($model->errors);

        }
        if($sn == '223229130-2' || $sn == '223229130-1'){
            return $this->success(true);
        }
        if(time() >= $model->start - 15*60 && time() < $model->start){
            return $this->success(true);
        }
        return $this->success(true);
    }

    /**
     * @desc   App - 获取预约课程详情
     * @author 李慧恩<lihuien@itsports.club>
     * @create: 2018-03-20
     * @param $data
     * @return array|null|\yii\db\ActiveRecord
     */
    public function actionGetClassYardDetail($data,$roomId)
    {
        $query = AboutYard::find()
            ->alias('ay')
            ->joinWith(['venueYard vy'])
            ->where(['ay.aboutDate'=>$data['today'], 'ay.member_id'=>$data['member_id'], 'vy.room_id'=>$roomId])
            ->andWhere(['<>','ay.status',5]);
        if($data['type'] == 'start'){
            $query->andWhere(['between', 'ay.about_start', $data['time'], $data['time']+15*60]);
        }elseif($data['type'] == 'in_class'){
            $query->andWhere(['and',['>=','ay.about_end',$data['time']], ['<=','ay.about_start',$data['time']]]);
        }else{
            $query->andWhere([ '<=','ay.about_end', $data['time']]);
            $query->orderBy('id DESC');
        }
        return $query->one();
    }
}
