<?php
namespace service\controllers;
use common\models\AboutClass;
use service\models\AboutClassnew;
use common\models\Func;
use common\models\Employee;
use service\base\BaseController;
use service\models\Coach;
use Yii;
use business\models\MemberCourseOrder;
use yii\data\ActiveDataProvider; 
use common\models\Action;
use common\models\ActionCategory;
use common\models\TrainTemplates;
use common\models\base\TrainTemplatess;
use common\models\TrainStage;
use common\models\MemberTemplate;
use common\models\MemberAction;
use common\models\ActionMember;
/**
* 
*/
class StatisticsController extends BaseController
{
    public $title;
    public $category_id;
    public $start_at;
    public $end_at;
    public $sorts;
    public $type;
    /**
     * @api {get} /service/Statistics/lessons  首页个人团队卖课数据统计
     * @apiName        1首页个人团队卖课数据统计
     * @apiGroup       Statistics
     * @apiParam  {string}            coach_id       私教id
     * @apiDescription   首页个人团队卖课数据统计
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/01/03
     * @apiSampleRequest  http://www.zhangdongxu.com/service/Statistics/Lessons
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    *{
    *   "message": "",
    *   "code": 1,
    *    "status": 200,
    *    "data": {
    *    "las": {
    *        "z_time": "2017/12",   时间
    *        "sum": "45",
    *        "course_number": "962"  总数
    *    },
    *    "class": [
    *    {
    *        "id": "265062",     id
    *        "status": "1",      状态   '1:未上课 2:取消预约 3:上课中 4:下课 5:过期 6:旷课(卡未被冻结)'
    *        "name": "小怪兽",    会员名称
    *        "start": "1526479200",   开始时间
    *        "end": "1526479320",     结束时间
    *        "num": 1,                以上多少节课
    *        "course_amount": "10",   总节数
    *        "product_name": "哪托专属"   私课名称
    *    },
    *    ]
    *    }
    *}
     * @apiSuccessExample {json}返回值详情（失败）
     *{
     *  "message": "",
     *  "code": 0,
     *  "status": 422,
     *  "data": []
     *}
     */
	public function actionLessons()
	{
//		$coach_id = Yii::$app->request->get('coach_id');
        $start_time = strtotime(date("Y-m-01 00:00:00",strtotime("-5 month")));
        $end_time = strtotime(date("Y-m-30 23:59:59"));
//        if ($this->user->IsManager) {
//
//            $name = array_column(Coach::find()->alias('e')->joinWith('organization o', FALSE)->where(['e.venue_id'=>$this->venueId, 'o.name'=>'私教部', 'e.status'=>1])->all(),'id');
//            $coach_id = $name;
//        }
        $query = MemberCourseOrder::find()
            ->alias('mco')
            ->joinWith('employeeS em',FALSE)
            ->joinWith('order o',FALSE)
            ->select(["from_unixtime(o.order_time, '%Y-%m') z_time",'COUNT(DISTINCT mco.id) AS sum','SUM(mco.course_amount) AS course_number'])
            ->where(['em.id'=>$this->coachId, 'mco.pay_status'=>1])->andWhere(['>','mco.money_amount',0]);;
            $query->andWhere(['between','o.order_time',$start_time,$end_time]);
            $data = $query
            ->groupBy(["DATE_FORMAT(from_unixtime(o.order_time),'%Y-%m')"])
            ->createCommand()
            ->queryAll();

            for ($i=0; $i <= 5 ; $i++) {
                $time = date("Y-m",strtotime(+$i.'month',$start_time));
                $a[$i] = $time;
                
            }
            $arr = array_diff($a,array_column($data, 'z_time'));
            foreach ($arr as $k => $v) {
                $tmp['z_time'] = $v;
                $tmp['sum'] = 0;
                $tmp['course_number'] = 0;
                $data[] = $tmp;
            }


        $sort = array_column($data, 'z_time');      
        // return $data;
        array_multisort($sort, SORT_ASC, $data);

            



//        $begintime=strtotime(date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y'))));
//        $endtime=strtotime(date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1));
		$data_timd = date('Y-m-d',time());
        $abouts = AboutClass::find()
            ->select('ac.id,ac.status,(mco.money_amount/mco.course_amount) as unit_price,ac.class_id,mco.member_id,ac.status,md.name,ac.start,ac.end,mco.overage_section,mco.course_amount,mco.course_amount,mco.product_name,mcod.sale_price,mc.card_number,md.pic')
            ->alias('ac')
            ->joinWith(['memberCourseOrderDetails mcod'=>function($q){//会员购买私课订单详情表
                $q->joinWith('memberCourseOrder mco');//会员课程订单表
            }])->joinWith('memberDetails md')//会员详细信息表
            ->joinWith('memberCard mc',FALSE)
            ->where(['ac.coach_id'=> $this->coachId,'ac.type'=>1])
            ->andWhere(['ac.status'=>[1,3,4]])->andWhere(['ac.class_date'=>$data_timd])
//            ->andWhere(['between','ac.create_at',$begintime,$endtime])
            ->asArray()->all();
        $day=[];
        foreach ($abouts as $about){
            $day[] = [
                'id' => $about['id'],//id
                'status' => $about['status'],//状态
                'card_number' => $about['card_number'],//卡号
                'pic' => $about['pic'],//照片
                'name' => $about['name'],//会员名字
                'start' => $about['start'],//上课时间
                'end' => $about['end'],//结束时间
                'num'=>$about['course_amount']-$about['overage_section'],//上课节数
                'course_amount'=>$about['course_amount'],//总节数
                'product_name'=>$about['product_name'],//私课名臣
                'money'=>sprintf("%.2f",$about['unit_price'])//钱
            ];
        }

        $var['las'] = $data;
        $var['class'] = $day;
        return $var;
	}

	public function salesDataAgainmonth(){
		$dateData = [];
		for($i=0;$i<=5;$i++){
			$key = "salesmonth".$i;
			$dateData[$key] = 0;
		}
		return $dateData;
	}

    /**
     * @api {get} /service/statistics/list?accesstoken=666   课程消息通知
     * @apiVersion  2.1.0
     * @apiName        课程消息通知
     * @apiGroup       message
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/Statistics/list?accesstoken=666
     * @apiDescription   1.0.1 新消息首页
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/17
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/statistics/list?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"code": 1,
     *"status": "success",
     *"message": "",
     *"data": [
     *{
     *          "content": "尚丽媛05月17日 13:59预约了PT常规课",
     *          "start": "05月17日 13:59",
     *          "status": 1
     *  },
     *      {
     *          "content": "尚丽媛05月17日 11:00预约了PT常规课",
     *          "start": "05月17日 11:00",
     *          "status": 1
     *      },
     *  {
     *          "content": "粱菁12月19日 18:30取消了MFT",
     *          "start": "12月19日 18:30",
     *          "status": 2
     *  },
     *  {
     *      "content": "杨慧磊05月21日 10:59取消了少儿MFT-100",
     *      "start": "05月21日 10:59",
     *      "status": 2
     *   },
     *  {
     *      "content": "1830071579104月27日 13:00取消了PT常规课",
     *      "start": "04月27日 13:00",
     *      "status": 2
     *   }
     *]
     *}
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */




    public function actionList()
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


        $query = AboutClass::find()
            ->alias('a')
            ->joinWith('memberDetails m',FALSE)
            ->joinWith('memberCourseOrderDetails o',FALSE)
            ->where(['a.coach_id'=>$ids, 'a.type'=>1, 'a.status'=>[1,2]])
            ->orderBy('a.create_at desc');

        return new ActiveDataProvider(['query'=>$query]);
    }

    /**
     * @api {get} /service/Statistics/classlist?accesstoken=666   会员上课列表
     * @apiVersion  2.1.0
     * @apiName        会员上课列表
     * @apiGroup       message
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/Statistics/classlist?accesstoken=666
     * @apiDescription   1.0.1 新消息首页
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/21
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/Statistics/classlist?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data": [
     *   {
     *       "dates": "2018-05-28",
     *       "count": 1,
     *       "list": [
     *           {
     *               "class_date": "2018-05-28",
     *               "id": "292281",
     *               "unit_price": "0.000000",
     *               "class_id": "58855",
     *               "member_id": "104976",
     *               "status": "1",
     *               "name": "胡美淇",
     *               "start": "1527480000",
     *               "end": "1527480360",
     *               "overage_section": "99",
     *               "course_amount": "100",
     *               "product_name": "伸展课程",
     *               "sale_price": null,
     *               "card_number": "09803289",
     *               "pic": null,
     *               "create_at": "1526975243"
     *           }
     *       ]
     *   }
    *],
     *}
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */

    public function actionClasslist()
    {
        $from_date = Yii::$app->request->get('from_date', '');

        if ($from_date != '' && strtotime($from_date) === false) {
            $from_date = 0;
        } elseif ($from_date != '') {
            $from_date = strtotime($from_date);
        } else {
            $from_date = 0;
        }

        $query = AboutClassnew::find()
            ->select('id')
//            ->groupBy(["DATE_FORMAT(FROM_UNIXTIME(start), '%Y-%m-%d')"])
            ->where(['coach_id'=>$this->coachId,'type'=>1])
            ->andWhere(['status'=>[1,3]])
//            ->andWhere(['>=','start',time()])
            ->andWhere(['>=','start',strtotime(date('Y-m-d'))])
            ->orderBy('class_date asc')->groupBy('class_date');
        $query = $this->groupMonthData($query, $from_date);
        $query->createCommand()->getRawSql();
        $query = new ActiveDataProvider(['query' => $query]);

        return $query;
    }



    private function groupMonthData($query, $from_date)
    {
        if ($from_date > 0) {
            $query->andWhere(['>=', 'create_at', $from_date]);
            $end_date = strtotime('+1 day', $from_date);
            $query->andWhere(['<', 'create_at', $end_date]);
        }
        return $query;
    }

    /**
     * 动作分类
     * @return Action detail
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionLists()
    {
        $data = ActionCategory::getCateTree();
        $data = Func::getDeepList($data);
        return $data;
    }

/**
     * @api {get} /service/statistics/memberlist?accesstoken=666   动作列表
     * @apiVersion  2.1.0
     * @apiName        动作列表
     * @apiGroup       message
     * @apiParam  {string}            title       搜索条件
     * @apiParam  {string}            category_id        类型搜索
     * @apiParam  {string}            type             搜索有氧  重量
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/statistics/memberlist?accesstoken=666
     * @apiDescription   1.0.1 动作列表
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/21
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/statistics/memberlist?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data": 
     *{
     *       "id": "17",       //id
     *       "title": "sdf",    //动作名称
     *       "type": "重量",     //类型  有氧重量
     *       "url": "",          //图片
     *       "titles": [
     *           "入门",        //难度
     *           "手臂"       //训练部位
     *       ]
     *   },
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionMemberlist()
    {
        $params = Yii::$app->request->queryParams;
        $query = Action::find()
            ->alias("a")
//            ->select('a.id')
            ->joinWith(['categorys c'])
            ->joinWith(['images i'])
            ->where(['a.is_delete'=>0])
            ->orderBy('a.created_at desc')
            ->groupBy('a.id');
        $this->customLoad($params);

        $query = $this->setWhereSearch($query);
        return new ActiveDataProvider(['query' => $query]);

    }
    /**
     * 判断搜索条件
     * @return Action detail
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function customLoad($data)
    {
        $this->title             = (isset($data['title']) && !empty($data['title'])) ? $data['title'] : null;  //搜索条件
        $this->category_id       = (isset($data['category_id']) && !empty($data['category_id'])) ? $data['category_id'] : null;  //搜索条件
        $this->type              = (isset($data['type'])) ? $data['type'] : null;  //搜索条件

    }
    /**
     * 搜索
     * @return Action detail
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function setWhereSearch($query)
    {
        if($this->type !== NUll){
            $query->andFilterWhere([
                'a.type' => $this->type,
            ]);
        }
        if($this->category_id!=["","",""]){
            $data = array_filter(explode(',',rtrim(implode(',',$this->category_id),',')));//条件
            foreach ( $data as $a ){
                $query->andWhere("FIND_IN_SET(".$a.",a.cate_id)");
            }
        }
        if(!empty($this->title)){
            $query->andFilterWhere(['like', 'a.title', $this->title]);
        }

        return $query;
    }
      /**
     * 服务端-模板-选择模板
     * @author zhangdongxu<zhangdongxu@itsports.club>
     * @return string
     */
    public function actionTemplatelist()
    {
        $data = TrainTemplates::find()
            ->select(['id','title'])
            ->asArray()
            ->all();
        return $data;
    }

    /**
     * @api {get} /service/Statistics/classmb  选择模板
     * @apiName        预设课程选择模板
     * @apiGroup       Statistics
     * @apiParam  {string}            tid     模板id
     * @apiDescription   预设课程选择模板
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/18
     * @apiSampleRequest  http://www.zhangdongxu.com/service/Statistics/classmb
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     * "code": 1
     * *"data": 
     *{
     *      "id": "13",             //模板id
     *       "title": "测试(模板名称)",   //模板名称   
     *       "stages": [
     *           {
     *               "title": "阶段1",    //阶段名称
     *               "id": "41",      阶段id
     *               "main": [
     *                   {
     *                       "action_id": 9,    //动作id
     *                       "title": "上斜哑铃卧推",     //动作名称
     *                       "ssentials": "阿法狗我二哥我", //动作要领
     *                       "unit": 1,         //'单位 1 次, 2 秒, 3分',
     *                       "number": 12,     //设置模板时保存的组数
     *                       "url": [
     *                           {
     *                               "url": ""   //动作图片示范
     *                           },
     *                           {
     *                               "url": ""
     *                           }
     *                       ]
     *                   },
      *      ]
       * },
    *}
     * @apiSuccessExample {json}返回值详情（失败）
     *{
     *  "message": "",
     *  "code": 0,
     *  "status": 422,
     *  "data": []
     *}
     */


    public function actionClassmb()
    {
        $templatelistid = Yii::$app->request->get('tid');
        $query = TrainTemplates::find()
            //->select('t.id,t.title,t.updated_at,s.id,s.template_id,s.title,s.length_time')
            ->alias('t')
            ->joinWith(['stages s'])
            ->where(['t.id'=>$templatelistid])
            ->groupBy('id');
       return new ActiveDataProvider(['query'=>$query]);
    }
/**
     * 服务端-模板-自定义模板
     * @author zhangdongxu<zhangdongxu@itsports.club>
     * @param status
     * @return array
     */
    public function actionCustoms()
    {
        $query = TrainStage::find()->where(['status'=>1]);
        return new ActiveDataProvider(['query'=>$query]);
    }
    /**
     * @api {post} /service/statistics/classmbj?accesstoken=666   会员模板信息保存
     * @apiVersion  2.1.0
     * @apiName        会员模板信息保存
     * @apiGroup       message
     * @apiParam  {string}            class_id       课程id
     * @apiParam  {string}            main        模板信息
     * @apiParam  {string}            type             搜索有氧  重量
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/statistics/classmbj?accesstoken=666
     * @apiDescription   1.0.1 新消息首页
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/21
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/statistics/classmbj?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data": 
     *{
     *     '修改成功'或'添加成功'
     *},
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionClassmbj()
    {
        $class_id = Yii::$app->request->post('class_id',0);
        $member_id = Yii::$app->request->post('member_id',0);
        $main = Yii::$app->request->post('main');
        $mins = json_decode($main,true);
        $a = [];
		foreach ($mins['stages'] as $key=>$value){
			if (!empty($value['main'])){
				$a[]=1;
			}
		}
		if (count($a) <= 0){
			return $this->error('你还没选择动作!');
		}
        $customer = new MemberTemplate;
        $data = MemberTemplate::findOne(['class_id'=>$class_id]);
        if(!empty($data))
        {
            $data->main = $main;
            $data->member_id = $member_id;
            $data->create_at = time();
            if($data->save())
            {
                return $this->success('修改成功');
            }else{
                return $this->error('修改失败');
            }
        }else{
            if(!empty($class_id))
            {
                $customer->class_id = $class_id;
                $customer->coach_id = $this->coachId;
                $customer->member_id = $member_id;
                $customer->main = $main;
                $customer->create_at = time();
                if($customer->save())
                {
                    return $this->success('添加成功');
                }else{
                    return $this->error('添加失败');
                }
            }
        }

    }
      /**
     * @api {get} /service/Statistics/Membermblist  会员上课详情动作模板展示
     * @apiName        会员上课详情动作模板展示
     * @apiParam  {string}            class_id     课程id
     * @apiDescription   会员上课详情动作模板展示
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/23
     * @apiSampleRequest  http://www.zhangdongxu.com/service/Statistics/Membermblist
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     * "code": 1
     * *"data": 
     *{
     *      "id": "13",             //模板id
     *       "title": "测试(模板名称)",   //模板名称   
     *       "stages": [
     *           {
     *               "title": "阶段1",    //阶段名称
     *               "id": "41",      阶段id
     *               "main": [
     *                   {
     *                       "action_id": 9,    //动作id
     *                       "title": "上斜哑铃卧推",     //动作名称
     *                       "ssentials": "阿法狗我二哥我", //动作要领
     *                       "unit": 1,         //'单位 1 次, 2 秒, 3分',
     *                       "number": 12,     //设置模板时保存的组数
     *                       "url": [
     *                           {
     *                               "url": ""   //动作图片示范
     *                           },
     *                           {
     *                               "url": ""
     *                           }
     *                       ]
     *                   },
      *      ]
       * },
    *}
     * @apiSuccessExample {json}返回值详情（失败）
     *{
     *  "message": "",
     *  "code": 0,
     *  "status": 422,
     *  "data": []
     *}
     */
    public function actionMembermblist()
    {
        $id = Yii::$app->request->get('class_id');
//        $id = 325642;
        $data = MemberTemplate::findOne(['class_id'=>$id]);
        return $data;
    }
    /**
     * @api {post} /service/statistics/member-action-remarks  会员动作组数和备注信息保存
     * @apiName        会员动作组数和备注信息保存
     * @apiParam  {string}            class_id     课程id
     * @apiParam  {string}            template_id     模板id
     * @apiParam  {string}            action_id     动作id
     * @apiParam  {string}            stage_id     阶段id
     * @apiParam  {string}            action_number     组数(数组)
     * @apiParam  {string}            content     内容
     * @apiDescription   会员动作组数和备注信息保存
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/6/01
     * @apiSampleRequest  http://www.zhangdongxu.com/service/statistics/member-action-remarks
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     * "code": 1
     * "data": 
     *{
     *  "message": "",
     *  "code": 0,
     *  "status": 422,
     *  "data": "保存成功"
    *}
     * @apiSuccessExample {json}返回值详情（失败）
     *{
     *  "message": "",
     *  "code": 0,
     *  "status": 422,
     *  "data": []
     *}
     */
    public function actionMemberActionRemarks()
    {
        $template_id = Yii::$app->request->post('template_id');
        $action_id   = Yii::$app->request->post('action_id');
        $class_id = Yii::$app->request->post('class_id');
        $stage_id = Yii::$app->request->post('stage_id');
        $action_number = Yii::$app->request->post('action_number');
        $content  = Yii::$app->request->post('content');
        $pic = Yii::$app->request->post('pic');
        $data = new MemberAction();
        if(!empty($pic))
        {
            foreach ($pic as $k)
            {
                $imgName = uniqid(md5(microtime(true))) . '.' . 'png';
                $err = Func::uploadBase64($imgName, $k);
                if(!$err){
                    $url[] = Func::getImgUrl($imgName);
                }else{
                    $url[] = '';
                }

            }
        }

        if(!empty($template_id) && !empty($action_id))
        {
            $data->template_id = $template_id;
            $data->action_id = $action_id;
            $data->action_number = json_encode($action_number);
            $data->class_id = $class_id;
            $data->stage_id = $stage_id;
            $data->content = $content;
            $data->create_at = time();
//            if(empty($url)) return $this->error('失败');
            if(!empty($url))
            {
                $data->url = json_encode($url);
            }
            if($data->save())
            {
                return $this->success("保存成功");
            }else{
                return $this->error("保存失败");
            }
        }else{
            return $this->error("参数错误");
        }

    }
//    会员上课中替换动作
    public function actionMemberActionSpecial()
    {
        $query = Action::find()
//            ->select(["a.id","a.type","a.title","i.url",'c.id as ids'])
        ->alias("a")
        ->joinWith(['categorys c'])
        ->joinWith(['images i'])
        ->where(['a.is_delete'=>0])
        ->orderBy('a.created_at desc')
        ->groupBy('a.id');
        return new ActiveDataProvider(['query'=>$query]);

    }


     /**
     * @api {post} /service/statistics/memberaction  会员运动图片保存
     * @apiName        会员运动图片保存
     * @apiParam  {string}            template_id     模板id
     * @apiParam  {string}            action_id     动作id
     * @apiParam  {string}            url[]     图片
     * @apiDescription   会员运动图片保存
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/6/01
     * @apiSampleRequest  http://www.zhangdongxu.com/service/statistics/memberaction
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     * "code": 1
     * "data": 
     *{
     *  "message": "",
     *  "code": 0,
     *  "status": 422,
     *  "data": "保存成功"
    *}
     * @apiSuccessExample {json}返回值详情（失败）
     *{
     *  "message": "",
     *  "code": 0,
     *  "status": 422,
     *  "data": []
     *}
     */
    public function actionMemberaction()
    {
        $template_id = Yii::$app->request->get('template_id');
        $action_id   = Yii::$app->request->get('action_id');
        $data = MemberAction::findOne(['template_id'=>$template_id,'action_id'=>$action_id]);
        if(!empty($template_id) && !empty($action_id))
        {
            if (isset($_FILES)){
                foreach ($_FILES as $k => $v)
                {
                    $arr = explode(".",  $_FILES[$k]['name']);
                    $Suffix = $arr[count($arr)-1];
                    $imgName = uniqid(md5(microtime(true))) . '.' . $Suffix;
                    $err = Func::uploadFile( $_FILES[$k]['tmp_name'], $imgName);
                    if(!empty($err)){
                        $this->addErrors(['enclosure'=>'上传失败']);
                        return false;
                    }
                    $urls[] = Func::getImgUrl($imgName);
                }
            }
            $url = json_encode($urls);
            $data->url = $url;
            if($data->save())
            {
                return $this->success("保存成功");
            }else{
                return $this->error("保存失败");
            }
        }else{
            return $this->error("参数错误");
        }
    }

//    上课中动作的增删改查
    public function actionCurriculum()
    {
        $main = Yii::$app->request->post('main');
        $class_id = Yii::$app->request->post('class_id');
        $data = MemberTemplate::findOne(['class_id'=>$class_id]);
        if(!empty($class_id))
        {
            $data->main = $main;
            if($data->save())
            {
                return $this->success("添加成功");
            }else{
                return $this->error("添加失败");
            }
        }else{
            return $this->error("参数错误");
        }

    }

//    动作的一系列详情
    public function actionMemberdetails()
    {
        $action_id = Yii::$app->request->get('action_id');
        $query = ActionMember::find()
            ->alias("a")
            ->joinWith(['categorys c'])
            ->joinWith(['images i'])
            ->where(['a.is_delete'=>0])
            ->andWhere(['a.id'=>$action_id])
            ->orderBy('a.created_at desc')
            ->groupBy('a.id');
        return new ActiveDataProvider(['query'=>$query]);

    }




}
