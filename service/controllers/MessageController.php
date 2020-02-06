<?php
namespace service\controllers;

use service\base\BaseController;
use common\models\AboutClass;
use common\models\Func;
use common\models\LeaveRecord;
use service\models\Approval;
use service\models\Coach;
use service\models\Evaluate;
use service\models\EvaluateList;
use service\models\Feedback;
use service\models\Assign;
use Yii;
use service\controllers\AuthController;

class MessageController extends BaseController
{
    /**
     * @api {get} /service/message/index?accesstoken=666   消息首页
     * @apiVersion  1.0.0
     * @apiName        消息首页
     * @apiGroup       message
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/message/index?accesstoken=666
     * @apiDescription   消息首页
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/13
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/message/index?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "code": 1,
        "status": "success",
        "message": "",
        "data": {
            "about": {
                "title": "课程预约",
                "count": 0,
                "content": "",
                "time": ""
            },
            "cancel": {
                "title": "取消课程",
                "count": 1,
                "content": "伍绪强取消了03月17日 15:30的PT常规课",
                "time": "15:30"
            },
            "miss": {
                "title": "会员爽约",
                "count": 5,
                "content": "张亚琴爽约了03月06日 18:30的PT常规课",
                "time": "18:30"
            },
            "leave": {
                "title": "会员请假",
                "count": 15,
                "content": "朱燕秋于03月23日 11:00请假",
                "time": "11:00"
            }
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionIndex()
    {

        $ids = $this->coachId;
        if ($this->user->employee->organization->name == '私教部'){
            //预约
            $about = AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>1])->orderBy('is_read asc, id desc')->one();
            if($about){
                $data['about']['title']='课程预约';
                $data['about']['count'] = (int)AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>1, 'is_read'=>0])->count();
                $member_name = Func::getRelationVal($about, 'memberDetails', 'name');
                $time = date('m月d日 H:i', $about->start);
                $product_name = Func::getRelationVal($about, 'memberCourseOrderDetails', 'product_name');
                $data['about']['content'] = "{$member_name}预约了{$time}的{$product_name}";
                $data['about']['time'] = date('H:i', $about->start);
            }else{
                $data['about']['title']='课程预约';
                $data['about']['count'] = 0;
                $data['about']['content'] = '';
                $data['about']['time'] = '';
            }

            //取消
            $cancel = AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>2])->orderBy('is_read asc, id desc')->one();
            if($cancel){
                $data['cancel']['title']='取消课程';
                $data['cancel']['count'] = (int)AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>2, 'is_read'=>0])->count();
                $member_name = Func::getRelationVal($cancel, 'memberDetails', 'name');
                $time = date('m月d日 H:i', $cancel->start);
                $product_name = Func::getRelationVal($cancel, 'memberCourseOrderDetails', 'product_name');
                $data['cancel']['content'] = "{$member_name}取消了{$time}的{$product_name}";
                $data['cancel']['time'] = date('H:i', $cancel->start);
            }else{
                $data['cancel']['title']='取消课程';
                $data['cancel']['count'] = 0;
                $data['cancel']['content'] = '';
                $data['cancel']['time'] = '';
            }
            //爽约
            $miss = AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>6])->orderBy('is_read asc, id desc')->one();
            if($miss){
                $data['miss']['title']='会员爽约';
                $data['miss']['count'] = (int)AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>6, 'is_read'=>0])->count();
                $member_name = Func::getRelationVal($miss, 'memberDetails', 'name');
                $time = date('m月d日 H:i', $miss->start);
                $product_name = Func::getRelationVal($miss, 'memberCourseOrderDetails', 'product_name');
                $data['miss']['content'] = "{$member_name}爽约了{$time}的{$product_name}";
                $data['miss']['time'] = date('H:i', $miss->start);
            }else{
                $data['miss']['title']='会员爽约';
                $data['miss']['count'] = 0;
                $data['miss']['content'] = '';
                $data['miss']['time'] = '';
            }

            //请假
            $leave = LeaveRecord::find()->joinWith('memberCourseOrder mco', FALSE)->where(['mco.private_id'=>$ids])->orderBy('is_read asc, id desc')->one();
            if($leave){
                $data['leave']['title']='会员请假';
                $data['leave']['count'] = (int)LeaveRecord::find()->joinWith('memberCourseOrder mco', FALSE)->where(['mco.private_id'=>$ids, 'is_read'=>0])->count();
                $member_name = Func::getRelationVal($leave, 'memberDetails', 'name');
                $time = date('m月d日 H:i', $leave->create_at);
                $data['leave']['content'] = "{$member_name}于{$time}请假";
                $data['leave']['time'] = date('H:i', $leave->create_at);
            }else{
                $data['leave']['title']='会员请假';
                $data['leave']['count'] = 0;
                $data['leave']['content'] = '';
                $data['leave']['time'] = '';
            }
        }else{
            //投诉
            // $complaint = Feedback::find()->where(['venue_id'=>$this->venueId])->orderBy('created_at asc, id desc')->one();
            $complaint = Feedback::find()->where(['venue_id'=>$this->venueId])->orderBy('created_at desc, id desc')->one();
            if($complaint){
                $data['complaint']['title']='会员投诉';
                // $data['complaint']['count'] = (int)Feedback::find()->where(['venue_id'=>$this->venueId])->orderBy('created_at asc, id desc')->count();
                $data['complaint']['count'] = (int)Feedback::find()->where(['venue_id'=>$this->venueId])->andWhere(['reply_time'=>null])->orderBy('created_at asc, id desc')->count();
                $member_name = Func::getRelationVal($complaint, 'member', 'username');
                $time = date('m月d日 H:i', $complaint->created_at);
                $data['complaint']['content'] = "{$member_name}于{$time}投诉";
                $data['complaint']['time'] = date('H:i', $complaint->created_at);
            }else{
                $data['complaint']['title']='会员投诉';
                $data['complaint']['count'] = 0;
                $data['complaint']['content'] = '';
                $data['complaint']['time'] = '';
            }
            //审批
            // $approval = Approval::find()->where(['venue_id'=>14])->orderBy('create_at asc, id desc')->one();
            $approval = Approval::find()->alias('a')->joinWith('approvalDetails ad')->andWhere(['ad.approver_id'=>$this->employeeId,'ad.status'=>1])->orderBy('id desc')->one();
            if ($approval){
                $data['approval']['title']='审批';
                // $data['approval']['count'] = (int)Approval::find()->where(['venue_id'=>14])->orderBy('create_at asc, id desc')->count();
                $data['approval']['count'] = (int)Approval::find()->where(['venue_id'=>$this->venueId,'create_id'=>$this->employeeId])->orderBy('create_at asc, id desc')->count();
                $member_name = Func::getRelationVal($approval, 'employee', 'name');
                $time = date('m月d日 H:i', $approval->create_at);
                $data['approval']['content'] = "{$member_name}于{$time}创建";
                $data['approval']['time'] = date('H:i', $approval->create_at);
            }else{
                $data['approval']['title']='审批';
                $data['approval']['count'] = 0;
                $data['approval']['content'] = '';
                $data['approval']['time'] = '';
            }


        }
//        $Evaluate = Evaluate::find()->where(['venue_id'=>$this->venueId])->orderBy('create_at desc, id desc')->one();
//        if ($Evaluate){
//            $data['Evaluate']['title']='评价';
//            $data['Evaluate']['count'] = (int)Evaluate::find()->where(['venue_id'=>$this->venueId])->orderBy('create_at asc, id desc')->count();
//            $member_name = Func::getRelationVal($Evaluate, 'member', 'username');
//            $time = date('m月d日 H:i', $Evaluate->create_at);
//            $venue_name = Func::getRelationVal($Evaluate, 'organization', 'name');
//            $data['Evaluate']['content'] = '收到'.$venue_name.'会员'.$member_name.'的评价';
//            $data['Evaluate']['time'] = $time;
//        }else{
//            $data['Evaluate']['title']='评价';
//            $data['Evaluate']['count'] = 0;
//            $data['Evaluate']['content'] = '';
//            $data['Evaluate']['time'] = '';
//        }

        return $this->success($data);
    }

    /**
     * @api {get} /service/message/indexs?accesstoken=666   消息首页
     * @apiVersion  1.0.1
     * @apiName        消息首页
     * @apiGroup       message
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/message/indexs?accesstoken=666
     * @apiDescription   1.0.1 新消息首页
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/13
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/message/indexs?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "code": 1,
        "status": "success",
        "message": "",
        "data": {
            "about": {
                "title": "课程预约",
                "count": 0,
                "content": "",
                "time": ""
            },
            "cancel": {
                "title": "取消课程",
                "count": 1,
                "content": "伍绪强取消了03月17日 15:30的PT常规课",
                "time": "15:30"
            },
            "miss": {
                "title": "会员爽约",
                "count": 5,
                "content": "张亚琴爽约了03月06日 18:30的PT常规课",
                "time": "18:30"
            },
            "leave": {
                "title": "会员请假",
                "count": 15,
                "content": "朱燕秋于03月23日 11:00请假",
                "time": "11:00"
            },
            "assign": {
                "title": "会员分配",
                "count": 0,
                "content": "",
                "time": ""
            }
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionIndexs()
    {
        // 获取当前用户可用权限的消息首页
        $auth = new AuthController($this->id, $this->module);
        $authModuleArr = $auth->getAuthList($this->employeeId);
        $auth->getTree($authModuleArr, $tree);
        if (empty($tree)) return $this->error('无权限');
        foreach ($tree as $k => $v) {
            if ($v['name'] != '消息') {
                unset($tree[$k]);
            }
        }
        $tree = array_values($tree);
        if (!isset($tree[0]['children']) || !is_array($tree[0]['children']) || count($tree[0]['children']) == 0) {
            return $this->error('无权限');
        }
        $authList = $tree[0]['children'];
        $authList = array_column($authList, 'name');

        $ids = $this->coachId;
        // if ($this->user->employee->organization->name == '私教部'){
        //预约
        if (in_array('课程预约', $authList)) {
            $about = AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>1])->orderBy('is_read asc, id desc')->one();
            if($about){
                $data['about']['title']='课程预约';
                $data['about']['count'] = (int)AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>1, 'is_read'=>0])->count();
                $member_name = Func::getRelationVal($about, 'memberDetails', 'name');
                $time = date('m月d日 H:i', $about->start);
                $product_name = Func::getRelationVal($about, 'memberCourseOrderDetails', 'product_name');
                $data['about']['content'] = "{$member_name}预约了{$time}的{$product_name}";
                $data['about']['time'] = date('H:i', $about->start);
            }else{
                $data['about']['title']='课程预约';
                $data['about']['count'] = 0;
                $data['about']['content'] = '';
                $data['about']['time'] = '';
            }
        }

        //取消
        if (in_array('取消课程', $authList)) {
            $cancel = AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>2])->orderBy('is_read asc, id desc')->one();
            if($cancel){
                $data['cancel']['title']='取消课程';
                $data['cancel']['count'] = (int)AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>2, 'is_read'=>0])->count();
                $member_name = Func::getRelationVal($cancel, 'memberDetails', 'name');
                $time = date('m月d日 H:i', $cancel->start);
                $product_name = Func::getRelationVal($cancel, 'memberCourseOrderDetails', 'product_name');
                $data['cancel']['content'] = "{$member_name}取消了{$time}的{$product_name}";
                $data['cancel']['time'] = date('H:i', $cancel->start);
            }else{
                $data['cancel']['title']='取消课程';
                $data['cancel']['count'] = 0;
                $data['cancel']['content'] = '';
                $data['cancel']['time'] = '';
            }
        }

        //爽约
        if (in_array('会员爽约', $authList)) {
            $miss = AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>6])->orderBy('is_read asc, id desc')->one();
            if($miss){
                $data['miss']['title']='会员爽约';
                $data['miss']['count'] = (int)AboutClass::find()->where(['coach_id'=>$ids, 'type'=>1, 'status'=>6, 'is_read'=>0])->count();
                $member_name = Func::getRelationVal($miss, 'memberDetails', 'name');
                $time = date('m月d日 H:i', $miss->start);
                $product_name = Func::getRelationVal($miss, 'memberCourseOrderDetails', 'product_name');
                $data['miss']['content'] = "{$member_name}爽约了{$time}的{$product_name}";
                $data['miss']['time'] = date('H:i', $miss->start);
            }else{
                $data['miss']['title']='会员爽约';
                $data['miss']['count'] = 0;
                $data['miss']['content'] = '';
                $data['miss']['time'] = '';
            }
        }

        //请假
        if (in_array('会员请假', $authList)) {
            $leave = LeaveRecord::find()->joinWith('memberCourseOrder mco', FALSE)->where(['mco.private_id'=>$ids])->orderBy('is_read asc, id desc')->one();
            if($leave){
                $data['leave']['title']='会员请假';
                $data['leave']['count'] = (int)LeaveRecord::find()->joinWith('memberCourseOrder mco', FALSE)->where(['mco.private_id'=>$ids, 'is_read'=>0])->count();
                $member_name = Func::getRelationVal($leave, 'memberDetails', 'name');
                $time = date('m月d日 H:i', $leave->create_at);
                $data['leave']['content'] = "{$member_name}于{$time}请假";
                $data['leave']['time'] = date('H:i', $leave->create_at);
            }else{
                $data['leave']['title']='会员请假';
                $data['leave']['count'] = 0;
                $data['leave']['content'] = '';
                $data['leave']['time'] = '';
            }
        }

        // 分配
        if (in_array('会员分配', $authList)) {
            $assign = Assign::find()->alias('a')->joinWith('management m', false)->joinWith('memberDetails md', false)->where(['a.coach_id' => $ids])->orderBy('a.id desc')->one();
            $data['assign']['title'] = '会员分配';
            if ($assign) {
                $data['assign']['count'] = (int)Assign::find()->where(['coach_id' => $ids, 'is_read' => 0])->count();
                $managementName = Func::getRelationVal($assign, 'management', 'name');
                $memeberName = Func::getRelationVal($assign, 'memberDetails', 'name');
                $data['assign']['content'] = "{$managementName}已将会员{$memeberName}分配给你";
                $data['assign']['time'] = date('H:i', $assign->create_at);
            } else {
                $data['assign']['count'] = 0;
                $data['assign']['content'] = '';
                $data['assign']['time'] = '';
            }
        }
        // }else{
        //投诉
        if (in_array('会员投诉', $authList)) {
            $complaint = Feedback::find()->where(['venue_id'=>$this->venueId])->orderBy('created_at desc')->one();
            if($complaint){
                $data['complaint']['title']='会员投诉';
                $data['complaint']['count'] = (int)Feedback::find()->where(['venue_id'=>$this->venueId])->orderBy('created_at asc, id desc')->count();
                $member_name = Func::getRelationVal($complaint, 'member', 'username');
                $time = date('m月d日 H:i', $complaint->created_at);
                $data['complaint']['content'] = "{$member_name}于{$time}投诉";
                $data['complaint']['time'] = date('H:i', $complaint->created_at);
            }else{
                $data['complaint']['title']='会员投诉';
                $data['complaint']['count'] = 0;
                $data['complaint']['content'] = '';
                $data['complaint']['time'] = '';
            }
        }
        //审批
        if (in_array('审批', $authList)) {
            $approval = Approval::find()->alias('a')->joinWith('approvalDetails ad')->andWhere(['ad.approver_id'=>$this->employeeId,'ad.status'=>1])->orderBy('id desc')->one();
            if ($approval){
                $data['approval']['title']='审批';
                $data['approval']['count'] = (int)Approval::find()->where(['venue_id'=>$this->venueId,'create_id'=>$this->employeeId])->orderBy('create_at asc, id desc')->count();
                $member_name = Func::getRelationVal($approval, 'employee', 'name');
                $time = date('m月d日 H:i', $approval->create_at);
                $data['approval']['content'] = "{$member_name}于{$time}创建";
                $data['approval']['time'] = date('H:i', $approval->create_at);
            }else{
                $data['approval']['title']='审批';
                $data['approval']['count'] = 0;
                $data['approval']['content'] = '';
                $data['approval']['time'] = '';
            }
        }
        // }
        
        if (in_array('团课评价', $authList)) {
            if ($this->user->employee->organization->name == '私教部'){
                
            } else {
                $evaluate = EvaluateList::find()->alias('e')->joinWith('groupClass gc', false)->where(['in', 'gc.coach_id', $this->employeeId])->orderBy('create_at DESC')->one();
                if ($evaluate){
                    $data['evaluate']['title']='团课评价';
                    $data['evaluate']['count'] = 1;
                    if($evaluate['display_status'] == 1)
                    {
                        $member_name = "匿名";
                    }else{
                        $member_name = Func::getRelationVal($evaluate, 'member', 'username');
                    }
                        $venue_name = Func::getRelationVal($evaluate, 'organization', 'name');
                        $data['evaluate']['content'] = '收到'.$venue_name.'会员'.$member_name.'的评价';
                        $data['evaluate']['time'] = date('H:i',$evaluate->create_at);
                }else{
                    $data['evaluate']['title']='团课评价';
                    $data['evaluate']['count'] = 0;
                    $data['evaluate']['content'] = '';
                    $data['evaluate']['time'] = '';
                }
            }
        }

        return $this->success($data);
    }

    /**
     * @api {get} /service/message/count?accesstoken=666   未读消息数
     * @apiVersion  1.0.0
     * @apiName        未读消息数
     * @apiGroup       message
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /coach/message/count?accesstoken=666
     * @apiDescription   未读消息数
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/14
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/message/count?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "code": 1,
        "status": "success",
        "message": "",
        "data": {
            "count": 36 //未读消息数
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionCount()
    {
        $aboutnum = AboutClass::find()->where(['coach_id'=>$this->coachId, 'type'=>1, 'status'=>[1,2,6], 'is_read'=>0])->count();
        $leavenum = LeaveRecord::find()->joinWith('memberCourseOrder mco', FALSE)->where(['mco.private_id'=>$this->coachId, 'is_read'=>0])->count();
        $count = intval($aboutnum+$leavenum);

        return $this->success(['count'=>$count]);
    }


}
