<?php
namespace service\controllers;

use common\models\MemberTemplate;
use service\base\BaseController;
use Tests\Behat\Gherkin\Node\PyStringNodeTest;
use  Yii;
use service\models\Mymember;
use yii\data\ActiveDataProvider;
use service\models\Memberlist;
use common\models\FollowWay;
use common\models\FollowMember;
use common\models\PhysicalTest;
use common\models\FitnessAssessment;
use common\models\MemberPhysicalTest;
use common\models\AboutClassrecord;
use common\models\MemberTemplaterecord;
use yii\db\Exception;
use yii\db\mssql\PDO;

class MymemberController extends BaseController
{
    public static $new_data = [];
    /**
     * @api {post} /service/mymember/memberlist?accesstoken=666   购课会员
     * @apiVersion  2.1.0
     * @apiName        购课会员
     * @apiGroup       mymember
     * @apiParam  {string}            name       搜索条件可以根据名字或手机号
     * @apiParam  {string}            tyep       跟进来源筛选(传来源id)
     * @apiParam  {string}            sort       排序(随便传不为空即可)
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/statistics/memberlist?accesstoken=666
     * @apiDescription   2.1.1 购课会员
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/21
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/mymember/memberlist?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data": [
     *{
     *       "id": "105421",       会员id
     *      "name": "yyy",         会员名字
     *       "content": "有意向购买私课",    跟进内容
     *       "create_at": "2018-06-05 15:52:27"   创建时间
     *   },
    *]
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionMemberlist()
    {
        $name = Yii::$app->request->post('name');//搜索
        $type = Yii::$app->request->post('type');//筛选
        $sort = Yii::$app->request->post('sort');//排序 (随便传点击的时候传过来不要为空)
        $query = Mymember::find()->alias('m')
            ->joinWith('memberCourseOrder mco', FALSE)
            ->joinWith('followMember fm',FALSE)
            ->where(['mco.private_id' => $this->coachId])
            ->orWhere(['m.private_id'=>$this->coachId])
            ->andWhere(['mco.product_type'=>1])
            ->andWhere(['mco.course_type'=>1])
            ->andWhere(['>','mco.money_amount',0])
            ->orderBy('mco.id desc');

        //团队会员
        if($this->user->isManager){
            if(!empty($sort))
            {
                $query = Mymember::find()->alias('m')->joinWith('followMember fm',FALSE)->where(['venue_id'=>$this->venueId])->orderBy('fm.actual_time desc');
            }else{
                $query = Mymember::find()->alias('m')->joinWith('followMember fm',FALSE)->where(['venue_id'=>$this->venueId])->orderBy('fm.create_at desc');
            }

        }
        if(!empty($sort))
        {
            $query->orderBy('fm.create_at desc');
        }else{
            $query->orderBy('fm.create_at desc');
        }
        if(!empty($name))
        {
            $query->andWhere(['or',['like','m.username',$name],['like','m.mobile',$name]]);
        }
        if(!empty($type))
        {
            $query->andWhere(['fm.follow_way_id'=>$type]);
        }
        return new ActiveDataProvider(['query'=>$query]);
    }
    /**
     * @api {post} /service/mymember/tiy?accesstoken=666   体验会员
     * @apiVersion  2.1.0
     * @apiName        体验会员
     * @apiGroup       mymember
     * @apiParam  {string}            name       搜索条件可以根据名字或手机号
     * @apiParam  {string}            tyep       跟进来源筛选(传来源id)
     * @apiParam  {string}            sort       排序(随便传不为空即可)
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/statistics/tiy?accesstoken=666
     * @apiDescription   2.1.1 体验会员
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/5/31
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/mymember/tiy?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data": [
     *{
     *       "id": "105421",       会员id
     *      "name": "yyy",         会员名字
     *       "content": "有意向购买私课",    跟进内容
     *       "create_at": "2018-06-05 15:52:27"   创建时间
     *   },
    *]
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionTiy()
    {
        $name = Yii::$app->request->post('name');//搜索
        $type = Yii::$app->request->post('type');//筛选
        $sort = Yii::$app->request->post('sort');//排序 (随便传点击的时候传过来不要为空)
        $query = Mymember::find()->alias('m')
            ->joinWith('memberCourseOrder mco', FALSE)
            ->joinWith('followMember fm',FALSE)
            ->where(['mco.private_id' => $this->coachId])
            ->orWhere(['m.private_id'=>$this->coachId])
            ->andWhere(['mco.product_type'=>1])
            ->andWhere(['=','mco.money_amount',0]);
        //团队会员
        if($this->user->isManager){
            if(!empty($sort))
            {
                $query = Mymember::find()->alias('m')->joinWith('followMember fm',FALSE)->where(['venue_id'=>$this->venueId])->orderBy('fm.actual_time desc');
            }else{
                $query = Mymember::find()->alias('m')->joinWith('followMember fm',FALSE)->where(['venue_id'=>$this->venueId])->orderBy('fm.create_at desc');
            }

        }
        if(!empty($sort))
        {
            $query->orderBy('fm.create_at desc');
        }else{
            $query->orderBy('fm.create_at desc');
        }

        if(!empty($name))
        {
            $query->andWhere(['or',['like','m.username',$name],['like','m.mobile',$name]]);
        }
        if(!empty($type))
        {
            $query->andWhere(['fm.follow_way_id'=>$type]);
        }
        return new ActiveDataProvider(['query'=>$query]);


    }
    /**
     * @api {post} /service/mymember/memberdetails?accesstoken=666   会员详情列表
     * @apiVersion  2.1.0
     * @apiName        会员详情列表
     * @apiGroup       mymember
     * @apiParam  {string}            member_id       会员id
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/statistics/memberdetails?accesstoken=666
     * @apiDescription   2.1.1 会员详情列表
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/6/2
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/mymember/memberdetails?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data": [
     *{
     *      "member_id": "79634",                           //会员id
     *       "name": "吴征",                                //会员名字
     *       "sex": "女",                                  //会员性别
     *       "mobile": "15333805189",                      //会员手机号
     *       "card_number": "10100886",                    //会员卡号
     *       "experience": {                              //体验课记录
     *           "complete": "暂无",                       //运动完成率
     *           "calorie": "暂无",                         //卡路里消耗
     *           "motion": "暂无",                          //训练方式
     *           "motion_qd": "暂无"                        //训练强度
     *       },
     *       "followmember": {                               //跟进记录
     *           "sum": 1,                                   //总数
     *           "id": "1",                                 //跟进记录id
     *           "content": "购买了东西但是吧",               //跟进内容
     *           "create_at": "2018-06-05 15:26:51"          //创建时间
     *           "title": "QQ",                             //跟进方式
     *           "follow_id": "1"                           //follow_way(跟进方式表自增id)
     *       }
     *   }
    *]
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionMemberdetails()
    {
        $member_id = Yii::$app->request->post('member_id');
//        $member_id = 79634;
        $query = Memberlist::find()
            ->alias('m')
            ->joinWith('memberCourseOrder mco', FALSE)
            ->joinWith('followMember fm',FALSE)
            ->orderBy('fm.create_at DESC')
            ->where(['m.id'=>$member_id]);
        return new ActiveDataProvider(['query'=>$query]);
    }
//    关联产品)
//    public function actionMemberRelation()
//    {
//        $member_id = 89167;
//        $query = MemberCourseOrder::find()
//                ->where(['member_id'=>$member_id])
//                ->where(['course_type'=>1])
//                ->andWhere(['product_type'=>1])
//                ->andWhere(['>','money_amount',0])
//                ->orderBy('create_at desc');
////                ->asArray()
////                ->all();
//            return new ActiveDataProvider(['query'=>$query]);
//
//    }
    /**
     * 服务端-跟进-跟进方式
     * @author zhangdongxu<zhangdongxu@itsports.club>
     * @param status
     * @return array
     */
    public function actionFollowway()
    {
        $data = FollowWay::find()
                ->select('id,title')
                ->asArray()
                ->all();
        return $data;

    }
     /**
     * @api {get} /service/mymember/Memberresult?accesstoken=666   添加跟进方式
     * @apiVersion  2.1.0
     * @apiName        添加跟进方式
     * @apiGroup       mymember
     * @apiParam  {string}              member_id    //会员id
     * @apiParam  {string}              content       //跟进内容
     * @apiParam  {string}              follow_way_id      //跟进方式id
     * @apiParam  {string}              next_time       //下次跟进时间
     * @apiParam  {string}              associates       //关联人姓名
     * @apiParam  {string}              associates_id     //关联人id
     * @apiParam  {string}              remind_id     //提醒ID
     * @apiParam  {string}              dite         //饮食建议
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/mymember/Memberresult?accesstoken=666
     * @apiDescription   2.1.1 添加跟进方式
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/6/3
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/mymember/Memberresult?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data": 
     *{
     *     '保存成功'
     *},
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionFollowMember()
    {
        $member_id = Yii::$app->request->post('member_id');
        $content = Yii::$app->request->post('content');
        $follow_way_id = Yii::$app->request->post('follow_way_id');
        $next_time = date('Y-m-d H:i:s',Yii::$app->request->post('next_time'));
        $associates = Yii::$app->request->post('associates','');
        $associates_id = Yii::$app->request->post('associates_id',0);
        $remind_id = Yii::$app->request->post('remind_id');
//        $file =  $_FILES;
//        if(!empty($file))
//        {
//            $imgName = uniqid(md5(microtime(true))) . '.' . 'mp3';
//            $pas = $file['pic']['tmp_name'];
//            $err = Func::uploadFile($pas, $imgName);
//            $url = Func::getImgUrl($imgName);
//        }else
//        {
//            $url = "";
//        }

        $data = new FollowMember();
        if(!empty($member_id))
        {
            $data->member_id = $member_id;
            $data->content = $content;
            $data->follow_way_id = $follow_way_id;
            $data->next_time = $next_time;
            $data->associates = $associates;
            $data->remind_id = $remind_id;
            $data->associates_id = $associates_id;
            $data->coach_id = $this->coachId;
//            $data->voice = $url;
             $data->operation = 0;
            if($data->save())
            {
                return $this->success("保存成功");
            }else{
                return $this->error("保存失败");
            }
        }else{
            return $this->error('参数失败');
        }
    }
    /**
     * @api {get} /service/mymember/followlist?accesstoken=666   跟进列表(带筛选)
     * @apiVersion  2.1.0
     * @apiName        跟进列表(带筛选)
     * @apiGroup       mymember
     * @apiParam  {string}              start    //开始时间
     * @apiParam  {string}              end       //结束时间
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/mymember/followlist?accesstoken=666
     * @apiDescription   2.1.1 跟进列表(带筛选)
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/6/8
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/mymember/followlist?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data":
    *{
     *"id": "2",                   //id
     *"content": "有意向购买私课",  //跟进内容
     *"title": "QQ"                  //跟进方式
     *},
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionFollowlist()
    {
        $start = strtotime(Yii::$app->request->post('start_time'));
        $end = strtotime(Yii::$app->request->post('end_time'));
        $member_id = Yii::$app->request->post('member_id');
        $query = FollowMember::find()
            ->alias('f')
            ->joinWith('followWay fw',FALSE)
            ->where(['f.member_id'=>$member_id])
            ->orderBy('f.create_at desc');
        if(!empty($start)&&!empty($end))
        {
            $query->joinWith('followWay fw',FALSE)
                    ->andWhere(['between','unix_timestamp(f.create_at)',$start,$end]);
        }
        return new ActiveDataProvider(['query'=>$query]);
    }
    /**
     * @api {get} /service/mymember/ergodic?accesstoken=666   遍历体测信息和体适能
     * @apiVersion  2.1.0
     * @apiName        遍历体测信息和体适能
     * @apiGroup       mymember
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/mymember/ergodic?accesstoken=666
     * @apiDescription   2.1.1 遍历体测信息和体适能
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/6/12
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/mymember/ergodic?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data":
     *[
     *     {
     *           "id": "4",                  //id
     *           "title": "维度测量",       //父级   名字
     *          "unit": "",                //单位
     *          "normal_range": "",         //范围
     *          "pid": "0",
     *          "children": [
     *          {
     *              "id": "8",                 //id
     *              "title": "大臂围",         // 子级   名字
     *              "unit": "cm",              // 单位
     *              "normal_range": "",         //范围
     *              "pid": "4"
     *      },
     * ]
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *  }
     */
    public function actionErgodic()
    {
        $ulist = FitnessAssessment::find()
                ->select(['id','title','unit','normal_range','pid'])
                ->where(['status'=>0])
                ->orderBy('create_at desc')
                ->asArray()
                ->all();
        $data  = self::RunNewData($ulist);

        $var = PhysicalTest::find()
                ->select(['id','title','unit','normal_range','pid'])
                ->where(['status'=>0])
                ->orderBy('create_at desc')
                ->asArray()
                ->all();
        $data1 = self::RunNewData($var);
        return array_merge($data,$data1);

    }

    public static function RunNewData($data)
    {
    	$new_data =[];
        foreach ($data as $key => $value)
        {
            if($value['pid'] == 0)
            {
                if(!in_array($value, $new_data))
                {
	                $new_data[$key] = $value;
                    foreach($data as $k => $v)
                    {
                        if($value['id'] == $v['pid'])
                        {
	                        $new_data[$key]['children'][] = $v;
                        }
//                        self::$new_data[$key]['a'] = false;
                    }
                }
            }
        }
        return $new_data;
    }
    /**
     * @api {POST} /service/mymember/member-aptamer?accesstoken=666   保存体测体适能信息
     * @apiVersion  2.1.0
     * @apiName        保存体测体适能信息
     * @apiGroup       mymember
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     * POST /service/mymember/member-aptamer?accesstoken=666
     * @apiParam  {string}              member_id    //会员id
     * @apiParam  {string}              type       //类型 1体侧 2 体适能
     * @apiParam  {string}              storage       //数组 包含体侧信息的二维数组
     * @apiDescription   2.1.1 保存体测体适能信息
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/6/12
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/mymember/member-aptamer?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data":
     * {
     *      "保存成功"
     * },
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *      "data":
     *          {
     *              "保存失败"
     *          },
     *  }
     */
    public function actionMemberAptamer()
    {
    
        $member_id = Yii::$app->request->post('member_id');
	    $type = Yii::$app->request->post('type');
        $storage = (Yii::$app->request->post('storage'));
        if (empty($member_id)) return $this->error('参数异常!');
        $types = [1,2];
        if (!in_array($type,$types)) return $this->error('参数异常');
        $time = date('Y-m-d',time());
        $py = MemberPhysicalTest::find()->where(["DATE_FORMAT(create_at,'%Y-%m-%d')"=>$time,'member_id'=>$member_id,'coach_id'=>$this->coachId])->asArray()->all();
        if (!empty($py)) return $this->error('今天已经体检过哦!');
        $msg =  $type==1? '体侧信息保存成功!': '体适能信息保存成功';
	    $a = [];
	    $storage = json_decode($storage,true);
	    foreach ($storage as $key=>$value){
	    	$a[$key][] = $member_id;
		    $a[$key][] = $value['cid'];
		    $a[$key][] = $value['num'];
		    $a[$key][] =$this->coachId;
		    $a[$key][] = $value['pid'];
		    $a[$key][] = $type;
	    }
	    $queryBuilder = Yii::$app->db->createCommand();
	    $sql = $queryBuilder->batchInsert('{{%member_physical_test}}', ['member_id', 'cid','physical_value','coach_id','pid','type'],$a);
	    $int = $sql->execute();
	    return $int ? $this->success($msg) :$this->error('保存失败!');
    }

    /**
     * @api {get} /service/mymember/class-record-list?accesstoken=666   历史记录列表
     * @apiVersion  2.1.0
     * @apiName        历史记录列表
     * @apiGroup       mymember
     * @apiParam  {string}              member_id    //会员id
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/mymember/class-record-list?accesstoken=666
     * @apiDescription   2.1.1 历史记录列表
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/6/19
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/mymember/class-record-list?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data":
     * [
     *       {
     *          "start_time": "06-08",   //日期
     *          "class_id": "325580",    // 课程id(about_class的自增id)
     *          "member_id": "97726",    //会员id
     *          "status": "4",           //状态1:未上课 2:取消预约 3:上课中 4:下课 5:过期 6:旷课(卡未被冻结) 7:旷课(团课爽约)
     *          "overage_section": "3",   //剩余节数
     *          "course_amount": "10"     //总节数
     *      }
     * ]
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *      "data":""
     *  }
     */
    public function actionClassRecordList()
    {
        $member_id = Yii::$app->request->get('member_id');
        $page = Yii::$app->request->get('page',1);
        $pagenum = 20;
	    $start = ($page -1) * $pagenum;
	    $datas = AboutClassrecord::find()
            ->alias('ac')
		    ->select(["DATE_FORMAT(ac.class_date,'%m-%d') start_time","ac.id as class_id","mco.member_id","ac.status","mco.overage_section","mco.course_amount","mco.course_amount"])
		    ->joinWith(['memberCourseOrderDetails mcod'=>function($q){//会员购买私课订单详情表
                $q->joinWith('memberCourseOrder mco',FALSE);//会员课程订单表
            }],FALSE)->joinWith('memberDetails md',FALSE)//会员详细信息表
            ->joinWith('memberCard mc',FALSE)
            ->where(['ac.type'=>1,'ac.status'=>4,'ac.member_id'=>$member_id,'ac.coach_id'=>$this->coachId]);
	    $count = clone $datas;
	    $count = $count->count();
	    $data = clone $datas;
	    $data = $data->orderBy('start_time DESC')->limit($pagenum)->offset($start)->asArray()->all();
	    $links =  'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?accesstoken='.$this->user->accesstoken."&member_id=".$member_id.'&page=';
        return $this->setpage($page,$count,$links,$data,$pagenum);
    }
    /**
     * @api {get} /service/mymember/class-record?accesstoken=666   会员上课玩历史模板信息
     * @apiVersion  2.1.0
     * @apiName        会员上课玩历史模板信息
     * @apiGroup       mymember
     * @apiParam  {string}              member_id    //会员id
     * @apiParam  {string}              class_id    //课程id(about_class自增id)
     * @apiPermission 管理员
     * @apiParamExample {json} 请求参数
     *   GET /service/mymember/class-record?accesstoken=666
     * @apiDescription   2.1.1 会员上课玩历史模板信息
     * <br/>
     * <span><strong>作    者：</strong></span>张东旭<br/>
     * <span><strong>邮    箱：</strong></span>zhangdongxu@itsprts.club
     * <span><strong>创建时间：</strong></span>2018/6/19
     * @apiSampleRequest  http://qaserviceapi.xingfufit.cn/service/mymember/class-record?accesstoken=666
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
     *{
     *"status": "success",
     *"message": "",
     *"code": 1,
     *"data":
     * {
     *       "id": "52",                              //自增id(member-template表)
     *       "class_id": "325714",                    //课程id
     * "main": {
     *       "id": 39,                                //模板id
     *       "title": "测试新增",                     //模板名称
     * "stages": [
     * {
     *       "title": "新增一",                        //阶段名称
     *       "id": 225,                                //阶段id
     * "main": [
     * {
     *      "action_id": 29,                           //动作id
     *      "title": "照片",                           //动作名称
     *      "ssentials": "潍坊",                       //动作要领
     *      "unit": 3,                                 //单位1 次, 2 秒 3 分
     *      "energy": 20,                              //热能消耗
     *      "number": 10,                              //组数或时间
     *      "mcontent": "",                            //动作信息备注
     *      "murl": [],                                //动作图片url
     *      "action_number": null                      //组数
     * },
     * "result": [
     * {
     *     "complete": "0",                           //训练完成率
     *     "calorie": "12465",                        //卡路里消耗
     *     "motion": "3",                             //运动方式
     *     "motion_qd": "84",                         //运动强度
     *     "everyday": "来咯回忆总",                  //每日评估
     *     "member_url": ""                           //会员签字图片
     *      }
     *      ]
     * @apiSuccessExample {json}返回值详情（失败）
     *  {
     *      "code": 0,                   //失败表示
     *      "status": "error",           //请求状态
     *      "message": ""    //失败原因
     *      "data":""
     *  }
     */

    public function actionClassRecord()
    {
        $member_id = Yii::$app->request->get('member_id');
        $class_id = Yii::$app->request->get('class_id');
        $data = MemberTemplaterecord::findOne(["member_id"=>$member_id,"class_id"=>$class_id]);
//        return new ActiveDataProvider(['query'=>$query]);
        return $data;
    }
	
	/**
	 * [自定义分页]
	 * Created by PhpStorm.
	 * User: 王亮亮
	 * Date: 2018/6/27
	 * Time: 11:07
	 * @E-mial:xmrwll@gmail.com
	 * @web:https://blog.csdn.net/pariese
	 * @param $page 当前页
	 * @param $count 总页
	 * @param $links 分页链接
	 * @param $data 数据
	 * @return mixed 带分页的结果
	 */
    public function setpage($page,$count,$links,$data,$pagenum){
	    $count =  ceil($count/$pagenum);
	    if ($page < $count){
		    $npage = $page+1;
	    }else{
		    $npage = $count;
	    }
	    
	    return ['data'=>$data,'_links' => [
		    'self'=>[
			    'href'=> $links.$page,
		    ],
		    'next'=>[
			    'href'=> $links.$npage,
		    ],
		    'last'=>[
			    'last'=> $links.$count,
		    ],
		    'pagecount'=>$count,
	    ]];
    }
    public function actionPy(){
    	$member_id = Yii::$app->request->get('member_id','1212');
    	$type = Yii::$app->request->get('type',1);
    	//回去这个教练下的这个会员最近的体侧时间
	    $db  = Yii::$app->db;
	    $sql     = "select DATE_FORMAT(create_at,'%Y-%m-%d') as time from cloud_member_physical_test where member_id= :member and coach_id= :coach_id and type = :type order by DATE_FORMAT(create_at,'%Y-%m-%d')  desc ";
	    $oCommand = $db->createCommand($sql);
	    $oCommand->bindParam(':member', $member_id, PDO::PARAM_STR);
	    $oCommand->bindParam(':coach_id', $this->coachId, PDO::PARAM_STR);
	    $oCommand->bindParam(':type', $type, PDO::PARAM_STR);
	    $result = $oCommand->queryOne();
		$newtime = $result['time'];
	    $query = MemberPhysicalTest::find()->alias('mpt')->where(["DATE_FORMAT(mpt.create_at,'%Y-%m-%d')"=>$newtime])->andWhere(['mpt.type'=>$type,'pid'=>0]);
	    return new ActiveDataProvider(['query'=>$query]);
    }

}