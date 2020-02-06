<?php

namespace common\models;

use backend\models\AuthMenu;
use backend\models\Employee;
use common\models\base\BindPack;
use common\models\base\CardCategory;
use common\models\base\EntryRecord;
use common\models\base\Functional;
use common\models\base\Member;
use common\models\base\MemberCard;
use common\models\base\MemberDetails;
use common\models\base\Module;
use common\models\base\Organization;
use common\models\base\Server;
use common\models\base\ConsumptionHistory;
use common\models\base\VenueLimitTimes;
use common\models\relations\MemberCourseOrderDetailsRelations;
use yii\base\Model;

class WipeData extends Model
{
    const MEMBER_TABLE   = ['cloud_member','cloud_member_details','cloud_consumption_history','cloud_member_card','cloud_member_cabinet','cloud_venue_limit_times'];
    const CARD_TABLE     = ['cloud_card_category','cloud_card_time','cloud_limit_card_number','cloud_bind_pack'];
    const ABOUT_TABLE    = ['cloud_about_class'];
    const CABINET_TABLE  = ['cloud_cabinet','cloud_cabinet_type'];
    const GROUP_TABLE    = ['cloud_group_class'];
    const CHARGE_TABLE   = ['cloud_charge_class','cloud_course_package_detail'];
    const VENUE_TABLE    = ['cloud_organization','cloud_classroom','cloud_seat'];
    const ORDER_TABLE    = ['cloud_order'];
    const STAFF_TABLE    = ['cloud_employee'];
    const SETTING        = ['会员管理','卡种管理','约课管理','柜子管理','订单管理','团课管理','私教管理','组织架构','员工管理'];
    const SETTING_TABLE = ['member','card','about','cabinet','order','group','charge','venue','yee'];
    const Tag             = ['user','credit-card','align-justify','tags'];
    const Icon            = ['btn-danger','btn-info','btn-warning','btn-primary'];
    const WHITE_ARR       =  ['白毛巾','白色毛巾'];
    const VIOLE_ARR       =  ['紫色毛巾','紫毛巾'];
    public $tablePrefix    = 'cloud_';
    public $whiteId;         //白色毛巾
    public $violetId;        //紫色毛巾
    public $memberId;
    public $memberCardId;
    public $venueId;
    public $error = [];
    public $pid;
    public $menuPid;
    /**
     * 云运动 - 数据库批量删除 - 执行sql
     * @param $sql
     * @return array
     */
    public function getDelSqlData($sql)
    {
        $queryBuilder = \Yii::$app->db->createCommand()->delete($sql);
        return $queryBuilder->execute();
    }
    /**
     * 云运动 - 数据库批量删除 - 执行查询数据
     * @author lihuien<lihuien@itsports.club>
     * @param $sql
     * @return array
     */
    public function getSqlData($sql)
    {
        return \Yii::$app->db->createCommand($sql)->queryAll();
    }
    /**
     * 云运动 - 数据库批量删除 - 入口文件
     * @author lihuien<lihuien@itsports.club>
     * @param $table string 入口
     * @return array
     */
    public function inlet($table)
    {
        switch ($table){
            case 'member':
                $this->delAssociation(self::MEMBER_TABLE);
                return true;
            case 'order':
                $this->delAssociation(self::ORDER_TABLE);
                return true;
            case 'card':
                $this->delAssociation(self::CARD_TABLE);
                return true;
            case 'cabinet':
                $this->delAssociation(self::CABINET_TABLE);
                return true;
            case 'group':
                $this->delAssociation(self::GROUP_TABLE);
                return true;
            case 'charge':
                $this->delAssociation(self::CHARGE_TABLE);
                return true;
            case 'about':
                $this->delAssociation(self::ABOUT_TABLE);
                return true;
            case 'venue':
                $this->delAssociation(self::VENUE_TABLE);
                return true;
            case 'yee':
                $this->delAssociation(self::STAFF_TABLE);
                return true;
            default:
                $tableArr = $this->getTableAll();
                $this->delAssociation($tableArr);
                return true;
        }
    }
    /**
     * 云运动 - 数据库批量删除 - 删除表数据
     * @author lihuien<lihuien@itsports.club>
     * @param $data array 入口
     * @param $type
     * @return array
     */
    public function delAssociation($data,$type = 'array')
    {
        if($type == 'array'){
            foreach ($data as $k=>$v){
                if($this->getSelectTable($v)){
                    $this->getDelSqlData($v);
                }
            }
        }else{
            $this->getDelSqlData($data);
        }
    }
    /**
     * 云论坛 - 数据库 - 获取数据库名称
     * @return array|string
     */
    public function getDatabaseName()
    {
        $sql             = 'select database()';
        $name            = self::getSqlData($sql);
        if(is_array($name) && !empty($name)) {
            $name = $name[0]['database()'];
        }else{
            $name = 'clouds';
        }

        return $name;
    }
    /**
     * 云运动 - 数据库批量删除 - 生成表sql
     * @author lihuien<lihuien@itsports.club>
     * @param $table string 入口
     * @return array
     */
    public function delTable($table)
    {
        $sql             = 'DELETE FROM'.' '.$table;
        $posts           = $this->getSqlData($sql);
        return $posts;
    }
    /**
     * 云运动 - 数据库批量删除 - 获取所有表名称
     * @author lihuien<lihuien@itsports.club>
     * @return array
     */
    public function getTableAll()
    {
        $sql             = 'show table status';
        $posts           = $this->getSqlData($sql);
        $data            = $this->getTableDetail($posts);
        return $data;
    }
    /**
     * 云运动 - 数据库批量删除 - 获取详细信息
     * @author lihuien<lihuien@itsports.club>
     * @param $posts array
     * @return array
     */
    public function getTableDetail($posts)
    {
        $data            = [];
        if($posts && !empty($posts)){
            foreach ($posts as $k=>$v){
                if($v['Name'] == 'cloud_admin' || $v['Name'] == 'cloud_migration'){
                   continue;
                }
                $data[] = $v['Name'];
            }
        }
        return $data;
    }
    /**
     * 云运动 - 数据库批量删除 - 获取删除数据
     * @author lihuien<lihuien@itsports.club>
     * @return array
     */
    public function getTableData()
    {
        $data = [];
        foreach (self::SETTING as $k=>$v){
            $arr = [];
            $arr['name']     = self::SETTING_TABLE[$k];
            $arr['comment']  = $v;
            $arr['tag']      = self::Tag[mt_rand(0,3)];
            $arr['icon']     = self::Icon[mt_rand(0,3)];
            $data[]          = $arr;
        }
        return $data;
    }
    /**
     * 云运动 - 数据库批量删除 - 查询所有表数据
     * @author lihuien<lihuien@itsports.club>
     * @param $table string 入口
     * @return array
     */
    public function getSelectTable($table)
    {
        $sql    = 'SELECT * FROM'.' '.$table;
        $num    = $this->getSqlData($sql);
        if($num && count($num)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 云运动 - 数据库批量删除 - 设置毛巾数据
     * @author lihuien<lihuien@itsports.club>
     * @return array
     */
    public function setBindPick()
    {
        $server = $this->getServerOne(self::WHITE_ARR);
        if($server && !empty($server)){
            $this->whiteId = $server['id'];
        }else{
           $wid = $this->setServerTowel('白色毛巾');
           $this->whiteId = $wid;
        }
        $serverV = $this->getServerOne(self::VIOLE_ARR);
        if($serverV && !empty($serverV)){
            $this->violetId = $serverV['id'];
        }else{
            $vid = $this->setServerTowel('紫色毛巾');
            $this->violetId = $vid;
        }
    }
    /**
     * 云运动 - 数据库批量删除 - 设置服务数据
     * @author lihuien<lihuien@itsports.club>
     * @param  $arr
     * @return array
     */
    public function getServerOne($arr)
    {
        return Server::find()->select('id')->where(['in','name',$arr])->one();
    }
    /**
     * 云运动 - 数据库批量删除 - 设置服务数据
     * @author lihuien<lihuien@itsports.club>
     * @param  $name
     * @return array
     */
    public function setServerTowel($name)
    {
        $server = new Server();
        $server->name        = $name;
        $server->create_at   = time();
        $server->description = '毛巾服务';
        if(!$server->save()){
            return false;
        }
        return $server->id;
    }
    public function setCardTowel()
    {
        $this->setBindPick();
        $card = CardCategory::find()->all();
        if(!empty($card)){
            foreach ($card as $k=>$v){
                 if(strpos($v['card_name'],'金爵') || strpos($v['card_name'],'尊爵')){
                     $this->saveBindPack($v['id'],$this->violetId);
                 }else{
                     $this->saveBindPack($v['id'],$this->whiteId);
                 }
            }
        }
        return $this->error;
    }
    public function saveBindPack($id,$tid)
    {
        $bink = new BindPack();
        $bink->card_category_id = $id;
        $bink->number           = 1;
        $bink->polymorphic_id   = $tid;
        $bink->polymorphic_type = 'server';
        $bink->status           = 2;
        if($bink->save()){
            return true;
        }
        return $bink->errors;
    }
    /**
     * 云运动 - 修改 - 获取会员信息
     * @create 2017/5/16
     * @author lihuien <lihuien@itsports.club>
     * @return bool
     */
    public function setMemberInputData()
    {
        return [
            [10200196,'陈建伟','女','','11:50:16','00:00:00','','键盘'],
            [10100281,'孟捷','女','','13:07:34','00:00:00','','键盘'],
            [10300196,'朱云黎','女','','10:59:35','00:00:00','','键盘'],
            [80000159,'李喜慧','女','','07:40:20','00:00:00','','键盘'],
            [10200209,'李本兵','男','','12:16:55','00:00:00','','键盘'],
            [0730000650,'崔新玲','女','','14:39:51','00:00:00','','键盘'],
            [10300196,'朱云黎','女','','07:46:30','00:00:00','','键盘'],
            [10100155,'王翔宇','男','','12:11:39','00:00:00','','键盘'],
        ];
    }
    /**
     * 云运动 - 修改 - 批量生成会员信息
     * @create 2017/5/16
     * @author lihuien <lihuien@itsports.club>
     * @return bool
     */
    public function setMemberBatchData()
    {
        $data = $this->setMemberInputData();
        foreach ($data as $k=>$v){
            $transaction  = \Yii::$app->db->beginTransaction();     //事务开始
            try{
               $member       = new Member();
               $memberDetail = new MemberDetails();
               $memberCard   = new MemberCard();
               $entry        = new EntryRecord();

                //插入会员表
                //判断会员是否存在(根据手机号码)

                $memberModel = Member::find()->where(["username"=>$v[1]])->asArray()->one();
                if(empty($memberModel)){
                    $password = '123456';
                    $password = \Yii::$app->security->generatePasswordHash($password);
                    $member->username      = isset($v[1])?$v[1]:'暂无';
                    $member->mobile        = '0';
                    $member->password      = $password;
                    $member->register_time = time();
                    $member->counselor_id  = 0;
                    $member->status        = 1;
                    if(!$member->save()){
                        $error = $member->errors;
                        return $error;
                    }
                    $this->memberId = $member->id;
                }else{
                    $this->memberId = $memberModel['id'];
                }
                //插入会员详情表
                $memberDetail_model = MemberDetails::find()->where(["member_id"=>$this->memberId])->asArray()->one();
                if(empty($memberDetail_model)){
                    $memberDetail->member_id = $this->memberId;
                    $memberDetail->name      = $v[1];
                    $memberDetail->sex       = $v[2] == '男' ? 1 : 2;
                    $memberDetail->created_at = time();
                    $memberDetail->recommend_member_id = 0;
                    if(!$memberDetail->save()){
                        $error = $member->errors;
                        return $error;
                    }
                }
                //判断会员卡号是否存在
                $memberCardModel = MemberCard::find()->where(["card_number"=>"$v[0]"])->asArray()->one();
                if(empty($memberCardModel)){
                    $memberCard->card_number      = "$v[0]";
                    $memberCard->create_at        = time();
                    $memberCard->active_time      = 0;
                    $memberCard->member_id        = $this->memberId;
                    $memberCard->card_category_id = 0;
                    $memberCard->payment_type     = 1;
                    $memberCard->payment_time     = 1;
                    $memberCard->is_complete_pay  = 1;
                    $memberCard->level            = 1;
                    $memberCard->status           = 1;
                    $memberCard->invalid_time     = 0;
                    $memberCard->payment_time     = 0;
                    if(!$memberCard->save()){
                        $error = $memberCard->errors;
                        return $error;
                    }
                    $this->memberCardId = $memberCard->id;
                }else{
                    $this->memberCardId = $memberCardModel['id'];
                }
                $this->getVenueId();
                $entry->entry_time = strtotime('2017-5-23 '.$v[4]);
                $entry->create_at  = time();
                $entry->member_id  = $this->memberId;
                $entry->member_card_id = $this->memberCardId;
                $entry->venue_id       = $this->venueId;
                if(!$entry->save()){
                    return $entry->errors;
                }
                if($transaction->commit() !== null){
                    return false;
                }
            }catch(\Exception $ex){
                $transaction->rollBack();
                return  $ex->getMessage();
            }
        }
    }
    /**
     * 云运动 - 验卡系统 - 获取场馆
     * @author lihuien <lihuien@itsports.club>
     * @create 2017/4/20
     * @return boolean
     */
    public function getVenueId()
    {
        $venue = Organization::find()->where(['like','name','大上海'])->asArray()->one();
        if(isset($venue['id'])){
            $this->venueId = $venue['id'];
        }else{
            $this->venueId = 2;
        }
    }
    /**
     * 云运动 - 验卡系统 - 获取场馆
     * @author lihuien <lihuien@itsports.club>
     * @create 2017/4/20
     * @return boolean
     */
    public static function getMemberCardNoName()
    {
        $memberCard = MemberCard::find()->where(['card_name'=>null])->all();
        $num = 0;
        if($memberCard && !empty($memberCard)){
            foreach ($memberCard as $k=>$v){
                $member  = MemberCard::findOne(['id'=>$v['id']]);
                $card = CardCategory::find()->where(['id'=>$v['card_category_id']])->asArray()->one();
                $member->card_name = $card['card_name'];
                if(!$member->save()){
                    return $member->errors;
                }
                $num++;
            }
        }
         return $num;
    }
    /**
     * 云运动 - 验卡系统 - 获取场馆
     * @author lihuien <lihuien@itsports.club>
     * @create 2017/4/20
     * @return boolean
     */
    public static function setApplyCard()
    {
        $ganArr     = Organization::find()->where(['or',['like','name','我爱运动'],['like','name','艾搏']])->asArray()->all();
        $idArr      = array_column($ganArr,'id');
        $venueArr   = Organization::find()->where(['in','pid',$idArr])->andWhere(['style'=>2])->asArray()->all();
        $zunArr     = Organization::find()->andWhere(['or',['like','name','大卫城'],['like','name','艾搏']])->asArray()->all();
        $venueArr   = array_column($venueArr,'id');
        $zunArr     = array_column($zunArr,'id');
        $memberCard = MemberCard::find()->where(['like','card_number','10%',false])->andWhere(['NOT',['venue_id'=>null]])->asArray()->all();
        $num = 0;
        if($memberCard && !empty($memberCard)){
            foreach ($memberCard as $k=>$v){
                if(in_array($v['venue_id'],$zunArr)){
                    foreach ($venueArr as $key=>$val){
                        $limit   = VenueLimitTimes::findOne(['member_card_id'=>$v['id'],'venue_id'=>$val]);
                        if(empty($limit)){
                            $times = new VenueLimitTimes();
                            $times->member_card_id = $v['id'];
                            $times->venue_id       = $val;
                            $times->total_times    = -1;
                            $times->overplus_times = -1;
                            $times->company_id     = null;
                            if(in_array($val,$zunArr) && $val != $v['venue_id']){
                                $times->level = 1;
                            }else{
                                $times->level = 2;
                            }
                            if(!$times->save()){
                                return $times->errors;
                            }
                        }
                    }
                }else{
                    foreach ($venueArr as $key=>$val){
                        $limit   = VenueLimitTimes::findOne(['member_card_id'=>$v['id'],'venue_id'=>$val]);
                        if(empty($limit)){
                            $times = new VenueLimitTimes();
                            $times->member_card_id = $v['id'];
                            $times->venue_id       = $val;
                            $times->total_times    = -1;
                            if(in_array($val,$zunArr)){
                                $times->overplus_times = 6;
                            }else{
                                $times->overplus_times = -1;
                            }
                            $times->company_id     = null;
                            if($val == $v['venue_id']){
                                $times->level = 2;
                            }else{
                                $times->level = 1;
                            }
                            if(!$times->save()){
                                return $times->errors;
                            }
                        }else{
                            if(in_array($val,$zunArr) &&  $limit->overplus_times == -1) {
                                $limit->overplus_times = 6;
                                $limit->save();
                            }
                        }
                    }
                }
                $num++;
            }
        }
        return $num;
    }
    /**
     * 云运动 - 验卡系统 - 获取场馆
     * @author lihuien <lihuien@itsports.club>
     * @create 2017/4/20
     * @return boolean
     */
    public static function setApplyZunCard()
    {
        $ganArr     = Organization::find()->where(['or',['like','name','我爱运动'],['like','name','艾搏']])->asArray()->all();
        $idArr      = array_column($ganArr,'id');
        $venueArr   = Organization::find()->where(['in','pid',$idArr])->asArray()->all();
        $zunArr     = Organization::find()->andWhere(['or',['like','name','大卫城'],['like','name','艾搏']])->asArray()->all();
        $venueArr   = array_column($venueArr,'id');
        $zunArr     = array_column($zunArr,'id');
        $memberCard = MemberCard::find()->where(['like','card_number','80%',false])->andWhere(['NOT',['venue_id'=>null]])->asArray()->all();
        $num = 0;
        if($memberCard && !empty($memberCard)) {
            foreach ($memberCard as $k => $v) {
                foreach ($venueArr as $key => $val) {
                    $limit = VenueLimitTimes::findOne(['member_card_id' => $v['id'], 'venue_id' => $val]);
                    if (empty($limit)) {
                        $times = new VenueLimitTimes();
                        $times->member_card_id = $v['id'];
                        $times->venue_id       = $val;
                        $times->total_times    = -1;
                        $times->overplus_times = -1;
                        $times->company_id     = null;
                        if (!$times->save()) {
                            return $times->errors;
                        }
                    }
                }
                $num++;
            }
        }
        return $num;
    }
    public static function setMemberVenue()
    {
        $card = MemberCard::find()->where(['venue_id'=>null])->asArray()->all();
        $num = 0;
        if(!empty($card)){
            foreach ($card as $k=>$v){
              $member =   Member::findOne(['id'=>$v['member_id']]);
              if(isset($member->venue_id) && !empty($member->venue_id)){
                  $memberCard = MemberCard::findOne(['id'=>$v['id']]);
                  $memberCard->venue_id   = $member->venue_id;
                  $memberCard->company_id = $member->company_id;
                  $memberCard->save();
                  $num ++ ;
              }
            }
        }
        return $num;
    }
    /**
     * 云运动 - 验卡系统 - 获取场馆
     * @author lihuien <lihuien@itsports.club>
     * @create 2017/4/20
     * @return boolean
     */
    public static function getMemberCompany()
    {
        $member = Member::find()->where(['or',['venue_id'=>null],['venue_id'=>0]])->andWhere(['company_id'=>null])->asArray()->all();
        $num = 0;
        if($member && !empty($member)){
            foreach ($member as $k=>$v){
                $model   = Member::findOne(['id'=>$v['id']]);
                $card    = Employee::find()->where(['id'=>$v['counselor_id']])->asArray()->one();
                if(!empty($card)){
                    $model->venue_id   = $card['venue_id'];
                    $model->company_id = $card['company_id'];
                    if(!$model->save()){
                        return $model->errors;
                    }
                    $num++;
                }
            }
        }
        return $num;
    }
    /**
     * 云运动 - 验卡系统 - 交换卡号
     * @author lihuien <lihuien@itsports.club>
     * @create 2017/4/20
     * @param  $numberOne
     * @param  $numberTwo
     * @return boolean
     */
    public static function setCardChangeNumber($numberOne,$numberTwo)
    {
        $cardOne = MemberCard::findOne(['card_number'=>$numberOne]);//20000006
        $cardTwo = MemberCard::findOne(['card_number'=>$numberTwo]);//09010215
        if(empty($cardOne) || empty($cardTwo)){
            return ['status'=>'error','data'=>'输入的卡号不正确'];
        }
        $id = $cardOne->id;
        $time = time();
        $cardOne->card_number = "{$time}";
        if(!$cardOne->save()){
            return ['status'=>'error','data'=>$cardOne->errors];
        }
        $cardOneId = MemberCard::findOne(['id'=>$id]);
        $cardOneId->card_number = $numberTwo;
        $cardTwo->card_number   = $numberOne;
        if($cardTwo->save() && $cardOneId->save()){
            return ['status'=>'success','data'=>'操作成功'];
        }
        return ['status'=>'error','data'=>'卡号交换失败2'];
    }
    /**
     * 云运动 - 验卡系统 - 获取卡
     * @author lihuien <lihuien@itsports.club>
     * @create 2017/4/20
     * @return boolean
     */
    public static function getCardChangeNumber()
    {
        $member = Member::find()
            ->alias('mm')
            ->select('mm.username')
            ->joinWith(['memberCard memberCard'])
            ->asArray()->all();
        return $member;
    }

    /**
     * 批量添加菜单，功能
     */
    public  function setModuleModel()
    {
        $topMenu    = AuthMenu::MENU;
        $subMenu    = AuthMenu::SUB_MENU;
        $func       = AuthMenu::FUNC;
        $menuDetail = AuthMenu::MENU_DETAIL;
        $idArr = [];
        foreach ($topMenu as $key=>$value){
            if($this->getModuleMenu($key)){
                continue;
            }
            $menu = new Module();
            $menu->name   = $value;
            $menu->e_name = $key;
            $menu->pid    = 0;
            $menu->create_at = time();
            $menu->update_at = time();
            if($menu->save()){
                $idArr[] = $menu->id;
            }else{
                return $menu->errors;
            }
        }
        foreach ($subMenu as $k1=>$v1){
            $this->pid = isset($idArr[$k1])?$idArr[$k1]:0;
            foreach ($v1 as $k2=>$v2){
                if($this->getModuleMenu($k2)){
                    continue;
                }
                $menu = new Module();
                $menu->name   = $v2;
                $menu->e_name = $k2;
                $menu->pid    = $this->pid;
                $menu->url    = $menuDetail[$k1][$k2];
                $menu->create_at = time();
                $menu->update_at = time();
                if(!$menu->save()){
                    return $menu->errors;
                }
            }
        }
        foreach ($func as $key=>$value){
            if($this->getFuncMenu($value[1])){
                continue;
            }
            $func = new Functional();
            $func->name   = $value[0];
            $func->e_name = $value[1];
            $func->note   = $value[2];
            $func->create_at = time();
            $func->update_at = time();
            if(!$func->save()){
                return $func->errors;
            }
        }
        return true;
    }

    public function getModuleMenu($name)
    {
        return Module::find()->where(['e_name'=>$name])->asArray()->one();
    }
    public function getFuncMenu($name)
    {
        return Functional::find()->where(['e_name'=>$name])->asArray()->one();
    }
    /**
     * 云运动 - 员工管理 - 批量修改员工信息
     * @author huanghua <huanghua@itsports.club>
     * @create 2017/6/24
     * @return boolean
     */
    public function getEmployee()
    {
        $employee              = Employee::find()->where(['or',['company_id'=>null],['venue_id'=>null]])->asArray()->all();
        if(!empty($employee)){
              foreach ($employee as $key => $value){
                  $organizationVenueId   = Organization::find()->where(['id'=>$value['organization_id']])->one();
                  $organizationCompanyId = Organization::find()->where(['id'=>$organizationVenueId['pid']])->one();
                  $employeeOne           = \common\models\base\Employee::findOne(['id'=>$value['id']]);
                  $employeeOne->venue_id   = $organizationCompanyId['id'];
                  $employeeOne->company_id = $organizationCompanyId['pid'];
                  if(!$employeeOne->save()){
                      return $employeeOne->errors;
                  }
              }
        }
        return true;
    }
    /**
     * 云运动 - 会员详情 - 续费记录到期时间批量修改
     * @author huanghua <huanghua@itsports.club>
     * @create 2017/10/23
     * @return boolean
     */
    public function consumptionUpdate()
    {
        $consumption         = ConsumptionHistory::find()->where(['due_date'=>null])->andWhere(['consumption_type'=>'card'])->asArray()->all();
        if(!empty($consumption)){
            foreach ($consumption as $key => $value){
                $memberCard               = MemberCard::find()->where(['id'=>$consumption['consumption_type_id']])->asArray()->one();
                $consumptionOne           = ConsumptionHistory::findOne(['id'=>$value['id']]);
                $consumptionOne->due_date = $memberCard['invalid_time'];
                if(!$consumptionOne->save()){
                    return $consumptionOne->errors;
                }
            }
        }
        return true;
    }
    /**
     * 云运动 - 会员详情 - 批量头像修改
     * @author huanghua <huanghua@itsports.club>
     * @create 2017/11/29
     * @return boolean
     */
    public function memberPicUpdate()
    {
        $memberDetails         = MemberDetails::find()->where(['pic'=>''])->asArray()->all();
        if(!empty($memberDetails)) {
            $memberDetailsId    = array_column($memberDetails,'id');
            $pic = MemberDetails::updateAll(['pic'=>null],['id'=>$memberDetailsId]);
        }
        return true;
    }
    /**
     * 云运动 - 会员状态 - 没有会员卡的会员批量修改把会员变为潜在会员
     * @author huanghua <huanghua@itsports.club>
     * @create 2017/11/8
     * @return boolean
     */
    public function memberUpdate()
    {
        $member = \common\models\Member::find()
            ->alias('mm')
            ->joinWith(['memberCard mc'])
            ->where(['mm.member_type'=>1])
            ->andWhere(['mc.member_id'=>null])
            ->asArray()
            ->all();
        if(!empty($member)){
            $memberId    = array_column($member,'id');
            $updateMember = Member::updateAll(['member_type'=>2],['id'=>$memberId]);
        }
        return true;
    }

    public function editMemberCard($data)
    {
        if(!$data['id']){
            return ['status'=>'error','message'=>'请求失败'];
        }
        $memberCard  = MemberCard::findOne(['id'=>$data['id']]);
        $memberCard->card_number = $data['number'];
        $memberCard->invalid_time = strtotime($data['expireDate']);
        if(!empty($data['createTime'])){
            $memberCard->create_at   = $data['createTime'];
            $consumptionHistory      = ConsumptionHistory::findOne(['consumption_type_id'=>$data['id']]);
            if(!empty($consumptionHistory)){
                $consumptionHistory->consumption_date = $data['createTime'];
                $consumptionHistory->save();
            }
        }
        if(!empty($data['cardName'])){
            $memberCard->card_name   = $data['cardName'];
        }
        if(!empty($data['attributes'])) {
            $memberCard->attributes = $data['attributes'];
        }
        if(!empty($data['money'])){
            $memberCard->amount_money   = $data['money'];
        }
        if(!empty($data['transferNumber'])){
            $memberCard->transfer_num   = $data['transferNumber'];
            $memberCard->surplus        = $data['transferNumber'];
        }
        if(!empty($data['activeTime'])){
            $memberCard->active_time   = $data['activeTime'];
        }
        if(!empty($data['postponeDate'])){
            $postponeDate = strtotime($data['postponeDate']);
            if(!empty($memberCard->status) && $memberCard->status == 4){
                $time         = ($postponeDate - $memberCard->create_at)/(24*60*60);
                $memberCard->active_limit_time = intval($time);
            }else if (!empty($memberCard->status) && $memberCard->status == 1){
                $endDate   = ($memberCard->invalid_time - $memberCard->active_time);
                $memberCard->active_time  = $postponeDate;
                $memberCard->invalid_time = $postponeDate + $endDate;
            }
        }
        if($memberCard->save()){
           return true;
        }
        return $memberCard->errors;
    }

    /**
     * 业务后台 - 会员管理 - 提醒会员生日
     * @param $id
     * @return array|bool
     */
    public function memberBirth($id)
    {
        $beginLastWeek = mktime(0,0,0,date('m'),date('d')-date('w')+1+7,date('Y'));
        $endLastWeek   = mktime(23,59,59,date('m'),date('d')-date('w')+7+7,date('Y'));
        $member = \common\models\Member::find()->alias('mm')
            ->joinWith(['memberDetails md'])
            ->joinWith(['venue venue'])
            ->andFilterWhere([
                'and',
                ['>=','date_format(md.birth_date,"%m-%d")',date('m-d',$beginLastWeek)],
                ['<=','date_format(md.birth_date,"%m-%d")',date('m-d',$endLastWeek)]
            ])
            ->andWhere(['NOT',['mm.mobile'=>null]])
            ->andWhere(['mm.venue_id'=>$id])
            ->asArray()
            ->all();
        return $member;
    }
    /**
     * 业务后台 - 会员管理 - 提醒会员生日
     * @param  $id
     * @return array|bool
     */
    public function memberBirthReminder($id)
    {
       $data = $this->memberBirth($id);
       if(isset($data) && !empty($data)){
           foreach ($data as $k=>$v){
              Func::memberBirthSendCode($v['mobile'],$v['venue']['name']);
           }
           return true;
       }
       return true;
    }
    /**
     * 业务后台 - 会员管理 - 设置私课课量
     * @return array|bool
     */
    public static function setCourseNum()
    {
        $query = \common\models\MemberCourseOrderDetails::find()->where(['or',['product_name'=>'PT'],['product_name'=>'HS']])->asArray()->all();
        $num = 0;
        if(!empty($query)){
            foreach ($query as $k=>$v){
                $memberCourse = \common\models\MemberCourseOrder::find()->alias('mco')
                    ->joinWith(['member member'])
                    ->where(['mco.id'=>$v['course_order_id']])
                    ->andWhere(['member.venue_id'=>[2,14]])
                    ->asArray()->one();
                if(!empty($memberCourse)){
                    $count = \common\models\MemberCourseOrderDetails::find()->where(['course_order_id'=>$v['course_order_id']])->count();
                    if($count > 1){
                        $num++;
                    }else{
                        $model = \common\models\MemberCourseOrderDetails::findOne(['id'=>$v['id']]);
                        $model->course_num  = $memberCourse['overage_section'];
                        $model->course_name = $model->product_name.'课程';
                        $model->save();
                        $num++;
                    }
                }
            }
        }
        return $num;
    }
    /**
     * 业务后台 - 会员管理 - 设置会员进场管次数
     * @return array|bool
     */
    public static function setLimitTimes()
    {
        $limit = \common\models\MemberCard::find()->alias('mc')->where(['IS','vlt.member_card_id',null])
            ->joinWith(['venueLimitTimesArr vlt'])
            ->asArray()->all();
        $num = 0;
        if(!empty($limit)){
           foreach ($limit as $key=>$value){
               $num++;
               if(!empty($value['card_category_id'])){
                   $card  = CardCategory::findOne(['id'=>$value['card_category_id']]);
                   $limit = LimitCardNumber::find()->where(['card_category_id' =>$value['card_category_id'],'status'=>[1,3]])->asArray()->all();
                   if(isset($limit)){
                       foreach($limit as $k=>$v){
                           $organ      = Organization::findOne(['id'=>$v['venue_id']]);
                           $result = self::setLimitVenue($value['id'],$v,$organ['pid']);
                           if($result !== true){
                               return $result;
                           }
                       }
                       $limitOne = LimitCardNumber::find()->where(['card_category_id' =>$value['card_category_id'],'venue_id'=>$card['venue_id']])->one();
                       if(!empty($limitOne)){
                           $result = self::setLimitVenue($value['id'],$limitOne,$organ['pid']);
                          if($result !== true){
                              return $result;
                          }
                       }
                   }
               }
           }
        }
        return $num;
    }
    /**
     * 业务后台 - 会员管理 - 设置会员进场管次数
     * @param  $id
     * @param  $data
     * @param  $pid
     * @return array|bool
     */
    public static function setLimitVenue($id,$data,$pid)
    {
        $limitVenue = new VenueLimitTimes();
        $limitVenue->member_card_id = $id;
        $limitVenue->venue_id       = $data['venue_id'];
        $limitVenue->total_times    = $data['times'];
        if(!empty($v['times'])){
            $limitVenue->overplus_times = $data['times'];
        }else{
            $limitVenue->overplus_times = $data['week_times'];
        }
        $limitVenue->week_times     = $data['week_times'];
        $limitVenue->venue_ids      = $data['venue_ids'];
        $limitVenue->company_id     = $pid;
        if(!$limitVenue->save()){
            return $limitVenue->errors;
        }
        return true;
    }
    /**
     * 业务后台 - 会员管理 - 设置为瑜伽馆
     * @return array|bool
     */
    public static function setMemberDanceByMember()
    {
        $card = \common\models\MemberCard::find()->alias('mc')
            ->joinWith(['member mm'])
            ->where(['like','mc.card_number','0950%',false])
            ->andWhere(['mm.venue_id'=>10])
            ->asArray()->all();
        if(!empty($card)){
            $mId = array_column($card,'member_id');
            return Member::updateAll(['venue_id'=>13],['id'=>$mId]);
        }
        return true;
    }
    /**
     * 业务后台 - 会员管理 - 获取会员信息
     * @param  $keyword
     * @param  $type
     * @return array|bool
     */
    public static function getMemberDanceData($keyword = '0950%',$type)
    {
        $num = 0;
        $card = \common\models\MemberCard::find()->alias('mc')
            ->joinWith(['member mm'])
            ->where(['like','mc.card_number',$keyword,false])
            ->asArray()->all();
        if($type == 1){
            $type = [13,10];
        }elseif ($type == 3){
            $type = [10];
        }else{
            $type = [13];
        }
        if(!empty($card)){
            foreach ($card as $k=>$v){
                foreach ($type as $ven){
                    $return = self::setVenueLimitData($v['id'],$ven);
                    if($return == 1){
                        $num++;
                    }
                }
            }
        }
        return $num;
    }
    /**
     * 业务后台 - 会员管理 - 获取会员信息
     * @param  $cardId   //卡种
     * @param  $venueId;
     * @return array|bool
     */
    public static function setVenueLimitData($cardId,$venueId)
    {
        $venueLimit = VenueLimitTimes::findOne(['member_card_id'=>$cardId,'venue_id'=>$venueId]);
        if(!empty($venueLimit)){
            if(!$venueLimit['total_times'] && !$venueLimit['week_times']){
                $venueLimit->total_times    = -1;
                $venueLimit->overplus_times = -1;
                $venueLimit->save();
                return 1;
            }
        }else{
            $limit = new VenueLimitTimes();
            $limit->venue_id = $venueId;
            $limit->member_card_id = $cardId;
            $limit->total_times    = -1;
            $limit->overplus_times = -1;
            $limit->level          = 1;
            $limit->save();
            return 1;
        }
        return 0;
    }
    /**
     * 业务后台 - 会员管理 - 批量修改无进场管权限的问题
     * @param  $number
     * @return array|bool
     */
    public static function updateMemberCardLimitData($number)
    {
        $num = 0;
        $card = \common\models\MemberCard::find()->alias('mc')
            ->joinWith(['member mm'])
            ->where(['like','mc.card_number',$number.'%',false])
            ->andWhere(['mc.venue_id'=>11])
            ->asArray()->all();
//        var_dump($card);die();
        if(!empty($card)){
            foreach ($card as $v){
                $limit = VenueLimitTimes::findOne(['venue_id'=>11,'member_card_id'=>$v['id']]);
                if(empty($limit)){
                    $times = new VenueLimitTimes();
                    $times->member_card_id = $v['id'];
                    $times->venue_id       = 11;
                    $times->total_times    = -1;
                    $times->overplus_times = -1;
                    $times->level          = $number == '092' ? 1 : 2;
                    $times->save();
                    $num++;
                }
            }
        }
        return $num;
    }
    /**
     * 业务后台 - 会员管理 - 设置为正式会员
     * @return array|bool
     */
    public static function setMemberTypeByCardId()
    {
        $card = \common\models\MemberCard::find()->alias('mc')
                ->joinWith(['member mm'])
                ->where(['mm.member_type'=>2])
                ->andWhere(['or',['<>','mc.usage_mode',2],['mc.usage_mode'=>null]])
                ->asArray()->all();
//        var_dump($card);die();
        if(!empty($card)){
           $mId = array_column($card,'member_id');
           return Member::updateAll(['member_type'=>1],['id'=>$mId]);
        }
        return true;
    }
    /**
     * 业务后台 - 会员管理 - 批量修改升级无金额信息
     * @return array|bool
     */
    public static function getMemberCardByPrice()
    {
       $card = MemberCard::find()->alias('mc')->where(['IS','amount_money',null])->asArray()->all();
       if(!empty($card)){
           foreach ($card as $k=>$v){
               $history = ConsumptionHistory::find()->where(['and',['IS NOT','consumption_amount',null],['consumption_type_id'=>$v['id']],['consumption_type'=>'card']])->one();
               if($history){
                   $mc = MemberCard::findOne(['id'=>$v['id']]);
                   $mc->amount_money = $history->consumption_amount;
                   $mc->save();
               }
           }
       }
    }
    /**
     * 业务后台 - 会员管理 - 批量删除会员卡消费记录
     * @return array|bool
     */
    public static function delMemberCardHistoryRecord()
    {
        $number = 0;
        $card = \common\models\MemberCard::find()->alias('mc')
            ->select('mc.id,mc.card_number,count(ch.id) as cid')
            ->joinWith(['consumptionHistory ch'])
            ->where(['ch.consumption_type'=>'card'])
            ->having(['>=','cid',2])->groupBy('ch.consumption_type_id')->asArray()->all();
        var_dump($card);die();
        if(!empty($card)){
            foreach ($card as $k=>$v){
                $history = ConsumptionHistory::find()->where(['consumption_type_id'=>$v['id']])->andWhere(['or',['consumption_type'=>'办卡消费'],['consumption_type'=>'card']])->asArray()->all();
                if($history){
                    $num   = count($history);
                    foreach ($history as $val){
                        if((empty($val['consumption_amount']) || empty($val['category'])) && $num >= 2){
                            $number++;
                            ConsumptionHistory::findOne(['id'=>$val['id']])->delete();
                            $num--;
                        }
                    }
                }
            }
        }
        return $number;
    }
    /**
     * 业务后台 - 会员管理 - 批量修改无类型会员卡
     * @return array|bool
     */
    public static function updateMemberCardType()
    {
        $number = 0;
        $cardArr = \common\models\MemberCard::find()->alias('mc')
            ->where(['and',['IS','card_type',null],['IS NOT','card_category_id',null]])->asArray()->all();
        if(!empty($cardArr)){
            foreach ($cardArr as $k=>$v){
                $number++;
                $card = CardCategory::findOne(['id'=>$v['card_category_id']]);
                $memberCard = MemberCard::findOne(['id'=>$v['id']]);
                if(!empty($card)){
                    $memberCard->card_type = $card->category_type_id;
                    $memberCard->save();
                }
            }
        }
        return $number;
    }
}