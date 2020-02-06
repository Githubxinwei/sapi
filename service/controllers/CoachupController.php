<?php
namespace service\controllers;

use Yii;
use service\base\BaseController;
use yii\data\ActiveDataProvider;
use service\models\Employee;
use service\models\Member;
use service\models\CoachFollow;
/**
* 
*/
class CoachupController extends BaseController
{
	/**
     * @api {get} /coachup/coachupadd/   教练对会员的评价
     * @apiVersion  2.0.6
     * @apiName        教练对会员的评价.
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *  GET /coachup/coachupadd/
     * {
     *        "member_id":"48324"        //会员ID
     *        "category":"1.qq 2.微信 3.电话 4.短信"  //跟进方式
     *        "create_at":"1525673341"   //跟进时间
     *        "content":"我在跟进"        //跟进内容
     * }
     * @apiDescription   教练对会员的评价
     * <br/>
     * <span><strong>作    者：</strong></span>zhangdongxu<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/7
     * @apiSampleRequest  http://www.zhangdongxu.com/service/coachup/coachupadd?accesstoken=11111
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *
     * {
	 *      "message": "",
	 *  	"code": 1,
	 * 	 	"status": 200,
	 *	   	"data": true
     * }
     * @apiSuccessExample {json}返回值详情（失败）
     * {
	 *      "message": "",
	 *  	"code": 0,
	 * 	 	"status": ,
	 *	   	"data": false
     * }
     */
	public function actionCoachupadd()
	{
		// echo 1;die;
		$member_id = Yii::$app->request->get('member_id');
		// $member_id = 221;
		$category = Yii::$app->request->get('category');
		// $category = 1;
		$content = Yii::$app->request->post('content');
		// $content = '1';
		$create_at = time();
        $coach_id = Yii::$app->request->get('coach_id', $this->coachId);
		// echo $coach_id;die;
		if(!empty($member_id) && !empty($coach_id))
		$data = new CoachFollow();
		$data->coach_id=$coach_id;
		$data->member_id=$member_id;
		$data->category=$category;
		$data->create_at=$create_at;
		$data->content=$content;
		$var = $data->save();
		// print_r($var);
		return $var;
	}
	/**
     * @api {get} /coachup/coachlist/   教练对会员的评展示
     * @apiVersion  2.0.6
     * @apiName        教练对会员的评展示.
     * @apiGroup       class
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *  GET /coachup/coachlist/
     *{
     *    "member_id":"48324"        //会员ID
     *}
     * @apiDescription   教练对会员的评展示
     * <br/>
     * <span><strong>作    者：</strong></span>zhangdongxu<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/7
     * @apiSampleRequest  http://www.zhangdongxu.com/service/coachup/coachlist?accesstoken=11111
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *     {
     *        "code": 1,
     *       "data": [
     *          {
     *             "id": "10",                //id
     *             "create_at": "2018-05-07", //时间
     *             "content": "2",            //内容
     *             "name": "刘贝"             //教练名字
     *       },
     *   ],
     *   "_links": {
     *       "self": {
     *           "href": "http://www.zhangdongxu.com/service/coachup/coachlist?accesstoken=214"
     *      }
     *  },
     * "_meta": {
     *   "totalCount": 2,
     *   "pageCount": 1,
     *   "currentPage": 1,
     *   "perPage": 20
     *}
     *}
     * @apiSuccessExample {json}返回值详情（失败）
     * {
	 *      "message": "",
	 *  	   "code": 0,
	 * 	   "status": ,
	 *	   "data": false
     * }
     */
	public function actionCoachlist()
	{
		$member_id = Yii::$app->request->get('member_id');
		// $member_id = 221;
		$query = CoachFollow::find()
			->alias('f')
			->joinWith('employee e',FALSE,"LEFT JOIN")
			->joinWith('member m',FALSE)
			->where(['f.member_id'=>$member_id])
			// ->select(["from_unixtime(f.create_at, '%Y-%m-%d') create_at","f.category","f.content","e.name"])
			->orderBy('f.create_at desc');
			// ->asArray()
			// ->all();
			// return $query;
			return new ActiveDataProvider(['query'=>$query]);
	}

}



