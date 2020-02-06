<?php
namespace service\controllers;
use Codeception\Module\Yii2;
use common\models\AboutClass;
use common\models\Func;
use common\models\Order;
use PHPUnit\Framework\Constraint\Count;
use service\models\Course;
use common\models\Employee;
use service\base\BaseController;
use service\models\MemberDietaryAdvice;
use Yii;
use business\models\MemberCourseOrder;
use yii\data\ActiveDataProvider;
use business\models\Member;
use common\models\Action;
use common\models\ActionCategory;
use common\models\TrainTemplates;
use common\models\TrainStage;
use common\models\MemberTemplate;
use common\models\MemberResult;
use common\models\MemberAction;
use common\models\FollowMember;
use yii\db\Exception;

/**
 *
 */
class CalculationController extends BaseController
{
    /**
     * @api {post} /service/calculation/acc?accesstoken=666   返回训练完成量和消耗卡路里
     * @apiVersion  2.1.0
     * @apiName        返回训练完成量和消耗卡路里
     * @apiGroup       message
     * @apiParam  {string}            class_id       课程id
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/calculation/acc?accesstoken=666
     * @apiDescription   2.1.1 训练结果
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/21
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/calculation/acc?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data": [
     *{
     *    "completion": 9  //运动完成率
     *},
     *{
     *    "klv": 48 //卡路里
    *}
    *]
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionAcc()
    {
        $id = Yii::$app->request->post('class_id');
        $template_id = Yii::$app->request->post('template_id');

        $var = MemberAction::find()
                ->select("id")
                ->where(['class_id'=>$id,'template_id'=>$template_id])
                ->asArray()
                ->all();
        $data = json_decode(MemberTemplate::findOne(['class_id'=>$id])->main,true);
        if($data['id'] == $template_id) {
            foreach ($data['stages'] as $k => $v) {
                foreach ($v['main'] as $k1 => $v1) {
                	$all =  (int)$v1['number'];
	                $mdatanum = MemberAction::findOne(['action_id'=>$v1['action_id'],'class_id'=>$id]);
	                $maction_number_count = json_decode($mdatanum['action_number'],true);
	                $unit = Action::findOne(['id'=>$v1['action_id']])->unit;
	                $energy = (int)Action::findOne(['id'=>$v1['action_id']])->energy;
	                if (isset($maction_number_count)){
		                $maction_number_count = $unit == 1 ?count($maction_number_count):$maction_number_count;
			                $menergy =((int)$energy * (int) $maction_number_count);
	                }else{
		                $menergy = 0;
	                }
                    $data['stages'][$k]['main'][$k1]['energy'] = $menergy;
                    
                    $a[] = $data['stages'][$k]['main'][$k1];
                }
            }
        }else{
            return false;
        }
        $b = count($a);
        $obj = intval(count($var)/$b*100);
        return array_merge(array('completion'=>$obj),array('klv'=>intval(array_sum(array_column($a,'energy')))));
    }

    /**
     * @api {get} /service/calculation/Memberresult?accesstoken=666   保存训练结果(体验课记录)
     * @apiVersion  2.1.0
     * @apiName        保存训练结果
     * @apiGroup       message
     * @apiParam  {string}              member_id    //会员id
     * @apiParam  {string}              class_id       //课程id
     * @apiParam  {string}              complete      //训练完成率
     * @apiParam  {string}              calorie       //卡路里消耗
     * @apiParam  {string}              motion       //运动方式
     * @apiParam  {string}              motion_qd     //运动强度
     * @apiParam  {string}              everyday     //每日评估
     * @apiParam  {string}              dite         //饮食建议
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/calculation/Memberresult?accesstoken=666
     * @apiDescription   1.0.1 新消息首页
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/31
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/calculation/Memberresult?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data": 
     *{
     *     ''添加成功'
     *},
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */

    public function actionMemberresult()
    {
        $member_id = Yii::$app->request->post('member_id');
        $class_id = Yii::$app->request->post('class_id');
        $complete = Yii::$app->request->post('complete',0);
        $calorie = Yii::$app->request->post('calorie',0);
        $motion = Yii::$app->request->post('motion');
        $motion_qd = Yii::$app->request->post('motion_qd');
        $everyday = Yii::$app->request->post('everyday');
        $food = Yii::$app->request->post('food');
	    $food =  json_decode($food,true);
	    if (empty($member_id) || empty($class_id)) return $this->error('参数异常!');
	    $a = [];
	    if (!empty($food)){
		    foreach ($food as $key=>$value){
			    if (!empty($value)){
				    foreach ($value as $k=>$v){
					    if (!empty($v)) {
						    $a[$key][$k]['con'] = $v['con'];
						    $imgName = uniqid(md5(microtime(true))) . '.' . 'png';
						    if (isset($v['pic'])){
							    $err = Func::uploadBase64($imgName, $v['pic']);
							    if(!$err){
								    $url = Func::getImgUrl($imgName);
							    }else{
								    $url = '';
							    }
						    }else{
							    $url = '';
						    }
						    $a[$key][$k]['pic'] = $url ;
					    }
				    }
			    }else{
				    $a[$key] = [];
			    }
			
		    }
	    }
	    $transaction= Yii::$app->db->beginTransaction();//创建事务
	    try{
        $mr = MemberResult::findOne(['class_id'=>$class_id,'member_id'=>$member_id]);
	    $var = new FollowMember();
	    $var->create_at = date("Y-m-d H:i:s");
	    $var->member_id = $member_id;
	    $var->coach_id = $this->coachId;
	    $var->operation = 6;
        if(!empty($mr)){
	        $var->content = "更改训练结果";
	        $mr->member_id = $member_id;
	        $mr->class_id = $class_id;
	        $mr->complete = $complete;
	        $mr->calorie = $calorie;
	        $mr->motion = $motion;
	        $mr->motion_qd = $motion_qd;
	        $mr->everyday = $everyday;
	        $msg = '更新';
	        if (!$mr->save()) {
	        	return $mr->errors;
	        }
        }else{
	        $msg = '添加';
	        $var->content = "添加训练结果";
	        $data = new MemberResult();
	        if (!empty($member_id) && !empty($class_id)) {
		        $data->member_id = $member_id;
		        $data->class_id = $class_id;
		        $data->complete = $complete;
		        $data->calorie = $calorie;
		        $data->motion = $motion;
		        $data->motion_qd = $motion_qd;
		        $data->everyday = $everyday;
		        if (!$data->save()) {
		        	return $data->errors;
		        }

	        }
        }
		    if(!$var->save()){
        	    return $var->errors;
		    }
		    if (!empty($a)){
			    $model = MemberDietaryAdvice::findOne(['member_id'=>$member_id,'about_class_id'=>$class_id]);
			    if (!isset($model)){
				    unset($model);
				    $model = new MemberDietaryAdvice();
			    }
			    $model->member_id =$member_id;
			    $model->coach_id = $this->coachId;
			    $model->about_class_id = $class_id;
			    $model->dietary_advice = json_encode($a);
			    if (!$model->save()) {
				    return $model->errors;
			    }
		    }
		    if (!$transaction->commit()) {
			    return $this->success($msg.'成功!');
		    }else{
		    	return $this->error($msg.'失败!');
		    }
	    }catch (Exception $e) {
		    $transaction->rollback();//如果操作失败, 数据回滚
		    return $e->getMessage();
	    }
	    
        
    }
/**
     * @api {get} /service/calculation/member-url?accesstoken=666   会员签名图片保存
     * @apiVersion  2.1.0
     * @apiName        会员签名图片保存
     * @apiGroup       message
     * @apiParam  {string}              class_id       //课程id
     * @apiParam  {string}              pic (post)       //图片 (json)
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/calculation/Memberurl?accesstoken=666
     * @apiDescription   1.0.1 新消息首页
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/31
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/calculation/member-url?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data": 
     *{
     *     ''添加成功'
     *},
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionMemberurl()
    {
        $pic = Yii::$app->request->post('pic');
        $class_id = Yii::$app->request->get('class_id');
        $var = MemberResult::findOne(['class_id' => $class_id]);
        $imgName = uniqid(md5(microtime(true))) . '.' . 'png';
        $err = Func::uploadBase64($imgName, $pic);
        if(!$err){
            $url = Func::getImgUrl($imgName);
        }else{
            $url = '';
        }
        if(empty($url)) return $this->error('失败');
        $var->member_url = $url;
        if($var->save()) {
            return $this->success($url, "成功");
        }else{
            return $this->error('失败');
        }

    }


    
}
