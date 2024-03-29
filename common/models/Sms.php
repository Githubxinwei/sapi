<?php
namespace common\models;

use yii\base\Model;
use backend\models\SmsRecordForm;
use yii;
class Sms extends Model
{

    private  $appid = 13670;            //在 SUBMAIL 应用集成中创建的短信应用 ID
    private  $to = '';                  //收件人手机号码，现在短信仅支持一对一模式，该参数现在仅能提交一个位联系人
    private  $project = '5yY5g2';       //项目标记 (ID)
    private  $vars = 'json';            //使用文本变量动态控制短信中的文本 json/string
    private  $timestamp = '';           //UNIX 时间戳
    private  $sign_type = '';           //API 授权模式（  md5 or sha1 or normal ）
    private  $signature = '';           //应用密匙 或 数字签名
    private  $appkey = 'e42d2fdd6c56b5bcf5e119d66cbe409b';

    public $url = 'https://api.mysubmail.com/message/xsend.json';


    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }



    /**
     * @云运动 - 给参数赋值
     * @author Xingsonglin <xingsonglin@itsports.club>
     * @create 2017/3/29
     * @return boolean
     * @inheritdoc
     */
    public function setSmsPara($to,$code)
    {
        $var = ['code'=>$code];
        $var = json_encode($var);
        $this->vars = $var;
        $this->to = $to;
        $this->timestamp = time();
        $this->sign_type = 'md5';
    }
    /**
     * @云运动 - 给参数赋值
     * @author Xingsonglin <xingsonglin@itsports.club>
     * @create 2017/3/29
     * @return boolean
     * @inheritdoc
     */
    public function setSmsMessage($to,$cardName)
    {
        $var = ['cardName'=>$cardName];
        $var = json_encode($var);
        $this->vars = $var;
        $this->to = $to;
        $this->timestamp = time();
        $this->sign_type = 'md5';
    }
    /**
     * @云运动 - 给参数赋值
     * @author Xingsonglin <xingsonglin@itsports.club>
     * @create 2017/3/29
     * @return boolean
     * @inheritdoc
     */
    public function setSmsVar($to,$name,$url)
    {
        $var = ['cardname'=>$name,'url'=>$url];
        $var = json_encode($var);
        $this->vars = $var;
        $this->to = $to;
        $this->timestamp = time();
        $this->sign_type = 'md5';
    }
    /**
     * @云运动 - 给参数赋值
     * @author Xingsonglin <xingsonglin@itsports.club>
     * @create 2017/3/29
     * @return boolean
     * @inheritdoc
     */
    public function setSmsVenue($to,$cardName)
    {
        $var = ['venue'=>$cardName];
        $var = json_encode($var);
        $this->vars = $var;
        $this->to = $to;
        $this->timestamp = time();
        $this->sign_type = 'md5';
    }
    /**
     * @云运动 - 给参数赋值
     * @author Xingsonglin <xingsonglin@itsports.club>
     * @create 2017/3/29
     * @return boolean
     * @inheritdoc
     */
    public function setNote($to,$course,$info)
    {
        $var = ['course'=>$course,'info'=>$info];
        $var = json_encode($var);
        $this->vars = $var;
        $this->to = $to;
        $this->timestamp = time();
        $this->sign_type = 'md5';
    }
    /**
     * @云运动 - 给参数赋值
     * @author zhumengke <zhumengke@itsports.club>
     * @create 2017/11/27
     * @return boolean
     * @inheritdoc
     */
    public function setFitnessDiet($to,$content)
    {
        $var = ['content'=>$content];
        $var = json_encode($var);
        $this->vars = $var;
        $this->to = $to;
        $this->timestamp = time();
        $this->sign_type = 'md5';
    }

    /**
     * @云运动 - 发送验证码
     * @author Xingsonglin <xingsonglin@itsports.club>
     * @create 2017/4/5
     * @return string
     * @inheritdoc
     */
    public function sendCode($to,$code){

        $this->setSmsPara($to,$code);
        $url = $this->url;

        $post_data = array (
            "appid" => $this->appid,
            "to" => $this->to,
            "vars" => $this->vars,
            "project"=>$this->project,
            "signature"=>$this->appkey
        );
      $this->commonData($url,$post_data,'7','');   //发送
    }

    /**
     * @云运动 - 会员卡激活成功发送信息
     * @author 朱梦珂 <zhumengke@itsports.club>
     * @create 2017/7/13
     * @return string
     * @inheritdoc
     */
    public function sendMessage($to,$cardName){

        $this->setSmsMessage($to,$cardName);
        $url = $this->url;

        $post_data = array (
            "appid" => $this->appid,
            "to" => $this->to,
            "vars" => $this->vars,
            "project"=>'UsPnh',
            "signature"=>$this->appkey
        );
        $this->commonData($url,$post_data,'6','');   //发送
    }
    
    public function request()
    {

    }

    /**
     * 用于发送短信
     * @author Huangpengju <Huangpengju@itsports.club>
     * @create 2017/5/12
     * @param $url
     * @param $data
     * @param $type
     * @param $again
     * @return mixed
     */
    public function commonData($url,$data,$type,$again)
    {
        $dataInfo  = $this->smsContent($data,$type);
//        $serverAddr = $_SERVER['SERVER_ADDR'];
//        $path == "47.93.202.248"
        $path       = $_SERVER['SERVER_NAME'];
        if($path == "http://product.aixingfu.net" || $path == "product.aixingfu.net"){
            $curl = curl_init(); // 启动一个CURL会话
            curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查  
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
            curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

            $tmpInfo = curl_exec($curl); // 执行操作
            if(!empty($tmpInfo)){
                $status = 1;
            }else{
                $status = 2;
            }
            if (curl_errno($curl)) {
                echo 'Errno'.curl_error($curl);//捕抓异常
            }
        if($again == ''){
            $sms     = new SmsRecordForm();
            $smsInfo = $sms->addSmsData($data,$dataInfo,$status,$type);
        }else{
            $sms     = new SmsRecordForm();
            $smsInfo = $sms->updateSmsData($data,$dataInfo,$status,$type,$again);
        }
            return $tmpInfo; // 返回数据
        }else{
            return false;
        }
    }

    /**
     * 处理短信模版内容中的参数替换
     * @author huanghua <huanghua@itsports.club>
     * @create 2017/9/18
     * @param $data
     * @param $type
     * @return mixed
     */
    public function smsContent($data,$type)
    {
        $var     = $data['vars'];
        $varData = json_decode($var,true);//数组里面的卡名和url
        $code = $data['project'];//发送码
        $info = $this->urlInfo();//根据发送码获取所有短信模版里面的内容
        $smsInfo = json_decode($info,true);//转换数据格式
       if(is_array($smsInfo['templates']) && !empty($smsInfo['templates'])){
           foreach($smsInfo['templates'] as $k => $value){
               if($value['template_id'] == $code){
                   $hh = $value['sms_content'];//根据发送码获取对应的短信模版内容

               }
           }
           switch($type)
           {
               case '1'          :
                   $dataInfo     = str_replace('@var(venue)',$varData['venue'],$hh);//ujVCz1  生日
                   break;
               case '2'           :
                   $dataInfo     = str_replace(['@var(cardname)','@var(url)'],[$varData['cardname'],$varData['url']],$hh);//vkzsB 办理卡
                   break;
               case '3'           :
                   $dataInfo     = str_replace('@var(course)',$varData['course'],$hh);//tUNI22  预约课程已经下课
                   break;
               case '4'        :
                   $dataInfo     = str_replace(['@var(company)','@var(venue)','@var(course)','@var(date)','@var(starttime)','@var(endtime)'],[$varData['company'],$varData['venue'],$varData['course'],$varData['date'],$varData['starttime'],$varData['endtime']],$hh);//A05aF2 预约私课信息
                   break;
               case '5'   :
                   $dataInfo     = str_replace(['@var(username)','@var(venue)','@var(course)','@var(number)'],[$varData['username'],$varData['venue'],$varData['course'],$varData['number']],$hh);//TlIkY 购买私课课程信息
                   break;
               case '6'          :
                   $dataInfo     = str_replace('@var(cardName)',$varData['cardName'],$hh);//UsPnh  购买卡以激活
                   break;
               case '7'          :
                   $dataInfo     = str_replace('@var(code)',$varData['code'],$hh);//5yY5g2  注册验证码
                   break;
               case '8'          :
                   $dataInfo     = $hh;   //j9RvQ4  潜在会员购卡
                   break;
               case '9'          :
                   $dataInfo     = str_replace('@var(cardname)',$varData['cardname'],$hh);//q4bLL3  迈步办卡
                   break;
               case '10'         :
                   $dataInfo     = str_replace('@var(content)',$varData['content'],$hh);//3gBMz1  健身目标、饮食计划
                   break;
               case '11'         :
                   $dataInfo     = str_replace(['@var(userName)','@var(cabinet)','@var(number)','@var(endTime)'],[$varData['userName'],$varData['cabinet'],$varData['number'],$varData['endTime']],$hh);//YtV4L 柜子到期提醒
                   break;
               default;
           }
           return $dataInfo;
       }

    }
    /**
     * 用会话获取所有短信内容
     * @author huanghua <huanghua@itsports.club>
     * @create 2017/9/18
     * @param $url
     * @return mixed
     */
    public function accessContent($url)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
//        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        return $tmpInfo; // 返回数据
//        print '<pre>';print_r(json_decode($tmpInfo,true));die();
    }
    /**
     * 拼接url获取短信内容
     * @author huanghua <huanghua@itsports.club>
     * @create 2017/9/9
     */
    public function urlInfo()
    {
        $urlData = 'https://api.mysubmail.com/message/template.json?appid='.$this->appid.'&signature='.$this->appkey;

        return $this->accessContent($urlData);   //发送
    }

    /**
     * @ 预约成功用于发送短信
     * @author Huangpengju <Huangpengju@itsports.club>
     * @create 2017/5/12 
     * @param $to
     * @param $info
     */
    public function sendInfo($to,$companyId,$venueId,$course,$classDate,$start,$end)
    {
        $this->setSendPara($to,$companyId,$venueId,$course,$classDate,$start,$end);
        $url = $this->url;

        $post_data = array (
            "appid" => $this->appid,
            "to" => $this->to,
            "vars" => $this->vars,
            "project"=>'A05aF2',
            "signature"=>$this->appkey
        );
        $this->commonData($url,$post_data,'4','');   //发送
    }
    /**
     * @云运动 - 给参数赋值
     * @author Huangpengju <Huangpengju@itsports.club>
     * @create 2017/5/12
     * @return boolean
     * @inheritdoc
     */
    public function setSendPara($to,$companyId,$venueId,$course,$classDate,$start,$end)
    {
        $var = ['company'=>$companyId,'venue'=>$venueId,'course'=>$course,'date'=>$classDate,'starttime'=>$start,'endtime'=>$end];
        $var = json_encode($var);
        $this->vars = $var;
        $this->to = $to;
        $this->timestamp = time();
        $this->sign_type = 'md5';
    }

    /**
     * @会员购买私课 - 发送短信
     * @author zhumengke <zhumengke@itsports.club>
     * @create 2017/7/21
     * @param $to
     * @param $info
     */
    public function memberCharge($to,$venueId,$memberName,$productName,$nodeNumber)
    {
        $this->setMemberCharge($to,$venueId,$memberName,$productName,$nodeNumber);
        $url = $this->url;

        $post_data = array (
            "appid" => $this->appid,
            "to" => $this->to,
            "vars" => $this->vars,
            "project"=>'TlIkY',
            "signature"=>$this->appkey
        );
        $this->commonData($url,$post_data,'5','');   //发送
    }
    /**
     * @云运动 - 给参数赋值
     * @author zhumengke <zhumengke@itsports.club>
     * @create 2017/7/21
     * @return boolean
     * @inheritdoc
     */
    public function setMemberCharge($to,$venueId,$memberName,$productName,$nodeNumber)
    {
        $var = ['venue'=>$venueId,'username'=>$memberName,'course'=>$productName,'number'=>$nodeNumber];
        $var = json_encode($var);
        $this->vars = $var;
        $this->to = $to;
        $this->timestamp = time();
        $this->sign_type = 'md5';
    }

    /**
     * @ 售卡成功用于发送短信
     * @author 朱梦珂 <zhumengke@itsports.club>
     * @create 2017/6/5
     * @param $to
     * @param $info
     */
    public function sendSellCardInfo($to,$info)
    {
        $this->setSmsPara($to,$info);
        $url = $this->url;

        $post_data = array (
            "appid" => $this->appid,
            "to" => $this->to,
            "vars" => $this->vars,
            "project"=>'j9RvQ4',
            "signature"=>$this->appkey
        );
        $this->commonData($url,$post_data,'8','');   //发送
    }
    /**
     * @ 登记上课成功用于发送短信
     * @author 黄华 <huanghua@itsports.club>
     * @create 2017/6/5
     * @param $to
     * @param $info
     * @param $course
     */
    public function sendClassInfo($to,$info,$course)
    {
        $this->setNote($to,$info,$course);
        $url = $this->url;

        $post_data = array (
            "appid" => $this->appid,
            "to" => $this->to,
            "vars" => $this->vars,
            "project"=>'tUNI22',
            "signature"=>$this->appkey
        );
        $this->commonData($url,$post_data,'3','');   //发送
    }

    /**
     * @迈步-办卡-发送短信
     * @create lihuien
     * @param $to       //发给谁
     * @param $info     //内容
     */
    public function setSellCardCode($to,$name,$deal)
    {
        $this->setSmsVar($to,$name,$deal);
        $url = $this->url;
        $post_data = array (
            "appid" => $this->appid,
            "to" => $this->to,
            "vars" => $this->vars,
            "project"=>'q4bLL3',
            "signature"=>$this->appkey
        );
        $this->commonData($url,$post_data,'9','');   //发送
    }
    /**
     * @迈步-办卡-发送短信
     * @create lihuien
     * @param $to       //发给谁
     * @param $name     //内容
     */
    public function setMemberBirthCode($to,$name)
    {
        $this->setSmsVenue($to,$name);
        $url = $this->url;
        $post_data = array (
            "appid" => $this->appid,
            "to" => $this->to,
            "vars" => $this->vars,
            "project"=>'ujVCz1',
            "signature"=>$this->appkey
        );
        $this->commonData($url,$post_data,'1','');   //发送
    }
    /**
     * @迈步-办卡-发送短信
     * @create lihuien
     * @param $to       //发给谁
     * @param $info     //内容
     */
    public function setAliPaySellCardCode($to,$cardName,$url)
    {
        $this->setSmsVar($to,$cardName,$url);
        $url = $this->url;
        $post_data = array (
            "appid" => $this->appid,
            "to" => $this->to,
            "vars" => $this->vars,
            "project"=>'vkzsB',
            "signature"=>$this->appkey
        );
        $this->commonData($url,$post_data,'2','');   //发送
    }
    /**
     * @会员维护 - 发送健身目标短信、发送饮食计划短信
     * @create zhumengke
     * @param $to       //发给谁
     * @param $name     //名称
     * @param $content  //内容
     */
    public function sendFitnessDiet($to,$content)
    {
        $this->setFitnessDiet($to,$content);
        $url = $this->url;
        $post_data = array (
            "appid" => $this->appid,
            "to" => $this->to,
            "vars" => $this->vars,
            "project"=>'3gBMz1',
            "signature"=>$this->appkey
        );
        $this->commonData($url,$post_data,'10','');   //发送
    }
    /**
     * 云运动 - 更柜管理 - 给会员提醒柜子
     * @author huanghua <huanghua@itsports.club>
     * @create 2017/12/15
     * @param $to      //发给谁
     * @param $name    //用户名
     * @param $typeTame //区域名
     * @param $cabinetNumber//柜子编号
     * @param $endRent//到期时间
     * @return string
     */
    public function sendCabinet($to,$name,$typeTame,$cabinetNumber,$endRent)
    {
        $this->setCabinetData($to,$name,$typeTame,$cabinetNumber,$endRent);
        $url = $this->url;
        $post_data = array (
            "appid" => $this->appid,
            "to" => $this->to,
            "vars" => $this->vars,
            "project"=>'YtV4L',
            "signature"=>$this->appkey
        );
        $this->commonData($url,$post_data,'11','');   //发送
    }


    /**
     * @云运动 - 给参数赋值
     * @author huanghua <zhumengke@itsports.club>
     * @create 2017/12/15
     * @return boolean
     * @inheritdoc
     */
    public function setCabinetData($to,$name,$typeTame,$cabinetNumber,$endRent)
    {
        $data = date("Y-m-d",$endRent);
        $var = ['userName'=>$name,'cabinet'=>$typeTame,'number'=>$cabinetNumber,'endTime'=>$data];
        $var = json_encode($var);
        $this->vars = $var;
        $this->to = $to;
        $this->timestamp = time();
        $this->sign_type = 'md5';
    }
}