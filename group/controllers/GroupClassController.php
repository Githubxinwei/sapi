<?php

namespace group\controllers;

use common\models\AboutClass;
use common\models\Func;
use common\models\Member;
use group\models\GroupClass;
use common\models\Seat;
use group\models\MyGroupClass;
use yii\data\ActiveDataProvider;

class GroupClassController extends BaseController
{
    public $modelClass = 'group\models\GroupClass';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'],$actions['delete'],$actions['update']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
    	$class_date = \Yii::$app->request->get('class_date',0);
    	$venue_id = \Yii::$app->request->get('venue_id',0);
        $query = GroupClass::find()->where(['coach_id'=>$this->employeeId])->orderBy('class_date ASC');
	    if (empty($venue_id)){
		    if (empty($class_date)){
			    $query =  $query->andWhere(['>=', 'class_date',date("Y-m-d",time())]);
		    }else{
			    $query = $query->andWhere(['=', 'class_date', $class_date]);
		    }
	    }else {
		    $query = $query->andWhere(['venue_id' => $venue_id]);
	    }
	    return new ActiveDataProvider(['query' => $query]);
    }

    /**
     * @api {get} /group/group-classes  团课列表
     * @apiName        1团课列表
     * @apiGroup       group-class
     * @apiParam  {string}            fields                可选,选择显示字段(例:fields=id,username,mobile)
     * @apiParam  {string}            sort                  排序（[id,sex]，例:sort=-id表示id desc, sort=id表示id asc）
     * @apiParam  {string}            page                  页码（可选，默认1）
     * @apiParam  {string}            per-page              每页显示数（可选，默认20）
     * @apiDescription   团课列表
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/04
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/group-classes
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "items": [
                {
                "id": "11043",
                "start": 1513496400,        //开始时间
                "end": 1513500000,          //结束时间
                "class_date": "2017-12-17", //日期
                "in_time": "0",             //上课打卡时间
                "out_time": "0",            //下课打卡时间
                "course_name": "普拉提",     //课程名称
                "seat_num": "63",           //座位数
                "about_num": "0"            //预约数
            },
            {
                "id": "11028",
                "start": 1513329000,
                "end": 1513332600,
                "class_date": "2017-12-15",
                "in_time": "0",
                "out_time": "0",
                "course_name": "普拉提",
                "seat_num": "63",
                "about_num": "0"
            },
            ],
            "_links": {
                "self": {
                    "href": "http://127.0.0.3/group/group-classes?accesstoken=001_1544587932&page=2"
                },
                "first": {
                    "href": "http://127.0.0.3/group/group-classes?accesstoken=001_1544587932&page=1"
                },
                "prev": {
                    "href": "http://127.0.0.3/group/group-classes?accesstoken=001_1544587932&page=1"
                },
                "next": {
                    "href": "http://127.0.0.3/group/group-classes?accesstoken=001_1544587932&page=3"
                },
                "last": {
                    "href": "http://127.0.0.3/group/group-classes?accesstoken=001_1544587932&page=3"
                }
            },
            "_meta": {
                "totalCount": 41,
                "pageCount": 3,
                "currentPage": 2,
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
     * @api {get} /group/group-class/seat   团课座位情况
     * @apiName        4团课座位情况
     * @apiGroup       group-class
     * @apiParam  {string}        id         团课ID
     * @apiDescription   团课座位情况
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/01/04
     * @apiSampleRequest  http://apiqa.aixingfu.net/group/group-class/seat
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
            {
                "id": "228",
                "classroom_id": "8",
                "seat_type": "1",
                "seat_number": "1",
                "rows": null,
                "columns": null,
                "seat_type_id": null,
                "is_anyone": 0          //空座
            },
            {
                "id": "1240",
                "classroom_id": "8",
                "seat_type": "1",       //类型：1普通，2VIP，3贵族
                "seat_number": "36",    //座号
                "rows": "3",            //行数
                "columns": "12",        //列数
                "seat_type_id": "11",
                "is_anyone": 1,         //有人
                "in_time":0,            //手环打卡时间
                "name": "杨馨",          //姓名
                "pic": "http://oo0oj2qmr.bkt.clouddn.com/2347491506073686.png?e=1506077286&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:N0KW8jkUWxRWSOqgYblUjeWj21A="//头像
            },
            {
                "id": "1241",
                "classroom_id": "8",
                "seat_type": "1",
                "seat_number": "37",
                "rows": "3",
                "columns": "13",
                "seat_type_id": "11",
                "is_anyone": 1,
                "name": "王中华",
                "pic": "http://oo0oj2qmr.bkt.clouddn.com/8643151505820332.png?e=1505823932&token=su_7pmwUM2iX2wn_2F0YSjOHqMWffbi6svEysW3S:zq86EJ-z3_Waoy_uhSJFKeXkFLE="
            }
        ]
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
    public function actionSeat($id)
    {
        $model = GroupClass::findOne(['id'=>$id, 'coach_id'=>$this->employeeId]);
        if(!$model) return $this->error('课不存在');

        AboutClass::updateAll(['status'=>3], ['and',['class_id'=>$id],['type'=>2],['status'=>1],['or',['is_print_receipt'=>1],['<>','in_time',0]],['<=','start',time()]]);
        AboutClass::updateAll(['status'=>4], ['and',['class_id'=>$id],['type'=>2],['status'=>3],['or',['is_print_receipt'=>1],['<>','in_time',0]],['<','end',time()]]);
        AboutClass::updateAll(['status'=>6], ['and',['class_id'=>$id],['type'=>2],['status'=>1],['is_print_receipt'=>2],['in_time'=>0],['<','start',time()]]);

        $seats = Seat::find()->where(['classroom_id'=>$model->classroom_id, 'seat_type_id'=>$model->seat_type_id])->orderBy('rows, columns')->asArray()->all();
        $data = [];
        foreach ($seats as $seat){
            $seat['is_anyone']   = '';
            $seat['in_time']     = '';
            $seat['status']      = '';
            $seat['member_type'] = '';
            $seat['name']        = '';
            $seat['pic']         = '';
            $aboutClass = AboutClass::find()->where(['and',['class_id'=>$id],['seat_id'=>$seat['id']],['type'=>2],['<>','status',2]])->asArray()->one();
            if(isset($aboutClass)){
                $seat['is_anyone']   = 1;
                $seat['in_time']     = $aboutClass['in_time'];
                $seat['status']      = $aboutClass['status'];
                $seat['member_type'] = Member::getMemberType($aboutClass);
                $aboutClass          = AboutClass::findOne($aboutClass['id']);
                $seat['name']        = Func::getRelationVal($aboutClass, 'memberDetails', 'name');
                $seat['pic']         = Func::getRelationVal($aboutClass, 'memberDetails', 'pic');
            }
            $data[] = $seat;
        }
        return $data;
    }

    /**
     * @api {get} /group/group-class/in   上课打卡
     * @apiName        5上课打卡
     * @apiGroup       group-class
     * @apiParam  {string}        id         团课ID
     * @apiDescription   上课打卡
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/01/05
     * @apiSampleRequest  http://apiqa.aixingfu.net/group/group-class/in
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
        "message": "已打上课卡",
        "code": 0,
        "status": 404,
        "type": "yii\\web\\NotFoundHttpException"
    }
     */
    public function actionIn($id)
    {
        $model = GroupClass::findOne(['id'=>$id, 'coach_id'=>$this->employeeId]);
        if(!$model) return $this->error('课不存在');
        if($model->in_time) return $this->error('已打上课卡');
        $model->in_time = time();
        $model->save();
//        AboutClass::updateAll(['status'=>3], ['and',['class_id'=>$model->id],['status'=>1],['<>','in_time',0],['type'=>2]]);
        return $this->success('上课打卡成功');
    }

    /**
     * @api {get} /group/group-class/out   下课打卡
     * @apiName        6下课打卡
     * @apiGroup       group-class
     * @apiParam  {string}        id         团课ID
     * @apiDescription   下课打卡
     * <br/>
     * <span><strong>作    者：</strong></span>张晓兵<br/>
     * <span><strong>邮    箱：</strong></span>zhangxiaobing@itsprts.club
     * <span><strong>创建时间：</strong></span>2017/01/05
     * @apiSampleRequest  http://apiqa.aixingfu.net/group/group-class/out
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
        "message": "还未打上课卡",
        "code": 0,
        "status": 404,
        "type": "yii\\web\\NotFoundHttpException"
    }
     */
    public function actionOut($id)
    {
        $model = GroupClass::findOne(['id'=>$id, 'coach_id'=>$this->employeeId]);
        if(!$model) return $this->error('课不存在');
        //if(!$model->in_time) return $this->error('还未打上课卡');
        $model->out_time = time();
        $model->save();
//        AboutClass::updateAll(['status'=>4], ['and',['class_id'=>$model->id],['status'=>3],['<>','in_time',0],['type'=>2]]);
//        AboutClass::updateAll(['status'=>6], ['class_id'=>$model->id,'status'=>1,'in_time'=>0,'type'=>2]);
        return $this->success('下课打卡成功');
    }
	/**
	 * @api {get} /group/group-class/venue   所有场馆
	 * @apiName        7所有场馆
	 * @apiGroup       group-class
	 * @apiDescription   所有场馆
	 * <br/>
	 * <span><strong>作    者：</strong></span>王亮亮<br/>
	 * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
	 * <span><strong>创建时间：</strong></span>2017/01/05
	 * @apiSampleRequest  http://apiqa.aixingfu.net/group/group-class/venue
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
	"message": "还未打上课卡",
	"code": 0,
	"status": 404,
	"type": "yii\\web\\NotFoundHttpException"
	}
	 */
    public function actionVenue(){
	    $query = \common\models\base\Organization::find()->where(['pid'=>[1,49]])->andWhere(['style'=>2])->andWhere(['is_allowed_join'=>1])->select('id,name');
	    return new ActiveDataProvider(['query' => $query]);
    }
	/**
	 * @api {get} /group/group-class/class-info   课程详情
	 * @apiName        8课程详情
	 * @apiGroup       group-class
	 * @apiDescription   课程详情
	 * <br/>
	 * <span><strong>作    者：</strong></span>王亮亮<br/>
	 * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
	 * <span><strong>创建时间：</strong></span>2017/01/05
	 * @apiSampleRequest  http://apiqa.aixingfu.net/group/group-class/class-info
	 * @apiSuccess (返回值) {json} data
	 * @apiSuccessExample {json}返回值详情（成功）
	{
	"message": "",
	"code": 1,
	"status": 200,
	"data": {
	"items": [
	{
	"id": "29902",
	"start": 1529452800,
	"end": 1529456400,
	"class_date": "2018-06-20",
	"in_time": "0",
	"out_time": "0",
	"seat_num": 30,
	"count": {
	"appointment": 0,//约课人数
	"cancel": 3, //取消约课人数
	"absenteeism": 1 // 旷课
	"normal": 0,//正常上完课的人数
	},
	"click": true //是否显示备注按钮
	}
	],
	"_links": {
	"self": {
	"href": "http://www.api.com/group/group-class/class-info?accesstoken=fXC0c05hU7Kgq6KxoJ8cvTxsFCqxQ65c_1529829964&page=1&per-page=1"
	},
	"next": {
	"href": "http://www.api.com/group/group-class/class-info?accesstoken=fXC0c05hU7Kgq6KxoJ8cvTxsFCqxQ65c_1529829964&page=2&per-page=1"
	},
	"last": {
	"href": "http://www.api.com/group/group-class/class-info?accesstoken=fXC0c05hU7Kgq6KxoJ8cvTxsFCqxQ65c_1529829964&page=674&per-page=1"
	}
	},
	"_meta": {
	"totalCount": 674,
	"pageCount": 674,
	"currentPage": 1,
	"perPage": 1
	}
	}
	}
	 * @apiSuccessExample {json}返回值详情（失败）
	"message": "",
	"code": 1,
	"status": 200,
	"data": {
	}
	 */
	public function actionClassInfo(){
		$class_date = \Yii::$app->request->get('class_date',0);
		
		$query =MyGroupClass::find()->where(['coach_id'=>$this->employeeId])->orderBy('start DESC');
		if ($class_date){
			$query = $query->andWhere(['=', 'class_date', $class_date]);
		}else{
			$query = $query->andWhere(['<', 'start', time()]);
		}
		return new ActiveDataProvider(['query'=>$query]);
	}
	/**
	 * @api {get} /group/group-class/venue   所有场馆
	 * @apiName        7所有场馆
	 * @apiGroup       group-class
	 * @apiDescription   所有场馆
	 * <br/>
	 * <span><strong>作    者：</strong></span>王亮亮<br/>
	 * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
	 * <span><strong>创建时间：</strong></span>2017/01/05
	 * @apiSampleRequest  http://apiqa.aixingfu.net/group/group-class/venue
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
	"message": "还未打上课卡",
	"code": 0,
	"status": 404,
	"type": "yii\\web\\NotFoundHttpException"
	}
	 */
	public function actionCurriculumEvaluation(){
	
	}
	/**
	 * @api {post} /group/group-class/curriculum-remark   课程备注
	 * @apiName        8课程备注
	 * @apiGroup       group-class
	 * @apiDescription   课程备注
	 * @apiParam  {string}        id         团课ID
	 * @apiParam  {string}        regiment_notes         备注
	 * <br/>
	 * <span><strong>作    者：</strong></span>王亮亮<br/>
	 * <span><strong>邮    箱：</strong></span>wangliangliang@itsprts.club
	 * <span><strong>创建时间：</strong></span>2017/01/05
	 * @apiSampleRequest  http://apiqa.aixingfu.net/group/group-class/venue
	 * @apiSuccess (返回值) {json} data
	 * @apiSuccessExample {json}返回值详情（成功）
	{
		"message": "备注成功!",
		"code": 1,
		"status": 200,
		"data": {
			"message": "备注成功!"
		}
	}
	 * @apiSuccessExample {json}返回值详情（失败）
	{
		"message": "备注成功!",
		"code": 1,
		"status": 200,
		"data": {
			"message": "备注成功!"
		}
	}
	 */
	public function actionCurriculumRemark(){
		$id = \Yii::$app->request->post('id','');
		$regiment_notes = \Yii::$app->request->post('regiment_notes','');
		if (empty($id)) return $this->error('参数异常');
		$model = GroupClass::findOne($id);
		$model->regiment_notes = $regiment_notes;
		return $model->save() ? $this->success('备注成功!'):$this->error('备注失败!');
	}
}