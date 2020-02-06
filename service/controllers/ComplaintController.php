<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/9 0009
 * Time: 下午 2:01
 */

namespace service\controllers;
use common\models\MemberDetails;
use service\base\BaseController;
use service\models\Feedback;
use common\models\Employee;
use service\models\Member;
use service\models\MemberDeposit;
use service\models\Organization;
use yii\data\ActiveDataProvider;
use Yii;
use common\models\Func;

class ComplaintController extends BaseController
{
    public $modelClass = 'service\models\Feedback';

    /**
     * @api {get} /service/complaint/list?accesstoken=000_1518243931  消息会员投诉列表
     * @apiName        1消息会员投诉列表
     * @apiGroup       Complaint
     * @apiParam  {string}            venue_id                场馆id 用于筛选
     * @apiParam  {string}            start                   开始时间戳
     * @apiParam  {string}            end                     结束时间戳
     * @apiParam  {string}            type                     和时间戳不共存d日 w周 m月
     * @apiDescription   消息会员投诉列表
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/27
     * @apiSampleRequest  http://qaservice.aixingfu.net/service/complaint/list
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
    "message": "",
    "code": 1,
    "status": 200,
    "data": [
    {
    "id": "15",
    "msg": "收到大学路舞蹈健身馆阎周璇的投诉!"
    },
    {
    "id": "1",
    "msg": "收到大学路舞蹈健身馆段稳丽的投诉!"
    }
    ]
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
    "message": "",
    "code": 0,
    "status": 422,
    "data": []
    }
     */

    public function actionList(){
        $venue_id = Yii::$app->request->get('venue_id', 0);
        $type = Yii::$app->request->get('type', 0);
        if(in_array($type, ['d', 'w', 'm'], TRUE)){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
        }else{
            $start = Yii::$app->request->get('start', 0) ?:strtotime(date('Y-m-d', time()));
            $end = Yii::$app->request->get('end', 0);
            $end = $end?$end==$start ? $start+86399 :$end+86399:$start+86399;
        }
        if (!$venue_id){
            $venue_id=Yii::$app->params['authVenueIds'];
        }
        $query = Feedback::find()->alias('fd')->select('fd.id,fd.content,fd.venue_id,fd.user_id,oz.name as venue_name ,mb.username as username')
            ->andWhere(['fd.venue_id'=>$venue_id])->orderBy('fd.create_at asc')->joinWith('organization oz',false)->joinWith('member mb');
        $query = Feedback::find()->orderBy('id desc');

        $query->andWhere(['venue_id'=>$venue_id]);
        if(empty($query)) return [];
        $provider = new ActiveDataProvider(['query' => $query]);
        return $provider;
    }
    /**
     * @api {get} /service/complaint/list?accesstoken=000_1518243931  会员投诉列表
     * @apiName        1会员投诉列表
     * @apiGroup       Complaint
     * @apiParam  {string}            venue_id                场馆id 用于筛选
     * @apiParam  {string}            start                   开始时间戳
     * @apiParam  {string}            end                     结束时间戳
     * @apiParam  {string}            type                     和时间戳不共存d日 w周 m月
     * @apiDescription   会员投诉列表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/27
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/complaints
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
    "message": "",
    "code": 1,
    "status": 200,
    "data": {
    "items": [
    {
    "id": 79,
    "type_id": 1,
    "from": "ios_customer",
    "company_id": 1,
    "venue_id": 76,
    "user_name": "13717669764",   //用户名
    "phone": "13717669764",  //用户手机号
    "content": "体现出咯嘛",       //内容
    "occurred_at": "0",
    "created_at": "1518140445", //时间
    "updated_at": "1518140445",
    "pics": "[\"http://oo0oj2qmr.bkt.clouddn.com/ff2d4b6ea4528a1c07e4684e8cf759885a7cfc1c36476.jpg?e=1518144045&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:1-lDHD5jQaTmw_QnNArzKwJnDEo=\", \"http://oo0oj2qmr.bkt.clouddn.com/25f299eeac309131eee9dddd6f8f21a95a7cfc1d5460f.jpg?e=1518144045&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:H3kQ9NlQVEQ5ztB7T3O75WKtF8c=\", \"http://oo0oj2qmr.bkt.clouddn.com/5e162d460fc1a9d8eb555c14413a74175a7cfc1d72571.jpg?e=1518144045&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:5IZkbglRS77htMshArZTtzGmccQ=\"]"
    }
    ],
    "_links": {
    "self": {
    "href": "http://apiqa.aixingfu.net//business/complaint/member?accesstoken=000_1518243931&page=1"
    }
    },
    "_meta": {
    "totalCount": 1,
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
    public function actionLists(){
        $venue_id = Yii::$app->request->get('venue_id', 0);
        $type = Yii::$app->request->get('type', 0);
        if(in_array($type, ['d', 'w', 'm'], TRUE)){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
        }else{
            $start = Yii::$app->request->get('start', 0) ?:strtotime(date('Y-m-d', time()));
            $end = Yii::$app->request->get('end', 0);
            $end = $end?$end==$start ? $start+86399 :$end+86399:$start+86399;
        }
        $query = Feedback::find()->andWhere(['between', 'created_at', $start, $end]);
        if (!$venue_id){
            $venue_id=Yii::$app->params['authVenueIds'];
        }
        $query->andWhere(['venue_id'=>$venue_id]);
        if(empty($query)) return [];
        $provider = new ActiveDataProvider(['query' => $query]);
        return $provider;
    }

    /**
     * @api {get} /service/complaint/details?accesstoken=000_1518243931&id=会员投诉详情
     * @apiName        2会员投诉详情
     * @apiGroup       Complaint
     * @apiParam  {string}            id                会员投诉id
     * @apiDescription   会员投诉详情
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/27
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/complaints/id
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
    "message": "",
    "code": 1,
    "status": 200,
    "data": {
    "id": 79,
    "type_id": 1,
    "from": "ios_customer",
    "company_id": 1,
    "venue_id": 76,  //场馆id
    "user_name": "13717669764",//用户名
    "phone": "13717669764",  //用户手机号
    "content": "体现出咯嘛",
    "occurred_at": "0",
    "created_at": "1518140445",
    "updated_at": "1518140445",
    "pics": "[\"http://oo0oj2qmr.bkt.clouddn.com/ff2d4b6ea4528a1c07e4684e8cf759885a7cfc1c36476.jpg?e=1518144045&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:1-lDHD5jQaTmw_QnNArzKwJnDEo=\", \"http://oo0oj2qmr.bkt.clouddn.com/25f299eeac309131eee9dddd6f8f21a95a7cfc1d5460f.jpg?e=1518144045&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:H3kQ9NlQVEQ5ztB7T3O75WKtF8c=\", \"http://oo0oj2qmr.bkt.clouddn.com/5e162d460fc1a9d8eb555c14413a74175a7cfc1d72571.jpg?e=1518144045&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:5IZkbglRS77htMshArZTtzGmccQ=\"]"
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
    public function actionDetails(){
        $id = Yii::$app->request->get('id', 0);
        if (empty($id)) return $this->error('请选择你要查看的详情!');
        $model = Feedback::findOne($id);
        return $model;
    }


    /**
     * @api {post} /service/complaint/complaint-reply?accesstoken=000_1518243931  12会员投诉回复
     * @apiName        2121会员投诉回复
     * @apiGroup       Complaint
     * @apiParam  {string}            id                会员投诉id
     * @apiParam  {string}            content           回复内容
     * @apiDescription   会员投诉详情
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>王亮亮@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/12/27
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/complaint/complaint-reply
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
    "message": "回复成功",
    "code": 1,
    "status": 200,
    -"data": {
    "message": "回复成功"
    }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
    "name": "Not Found",
    "message": "已回复！",
    "code": 0,
    "status": 404,
    "type": "yii\\web\\NotFoundHttpException"
    }
     */

    public function actionComplaintReply(){
        $id = Yii::$app->request->post('id',0);
        $content = Yii::$app->request->post('content',0);
        if (empty($id) ||empty($content) ){
            return $this->error('必传参数不能为空！');
        }
        $query = Feedback::findOne($id);
        if (!empty($query->reply_time) && !empty($query->reply_content) && !empty($query->reply_person) ){
            return $this->error('已回复！');
        }
        $query->reply_time = time();
        $query->reply_content = $content;
        $query->reply_person = $this->employeeId;
        if ($query->save()){
            return $this->success('回复成功');
        }
        return $this->error('回复失败！');

    }

}