<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15
 * Time: 15:18
 */

namespace coach\models;

use common\models\base\Employee;
use Yii;
use yii\base\Model;
use common\models\Func;
use common\models\AboutClass;
use common\models\MemberCourseOrder;

class Course extends Model
{
    public $searchDateStart;
    public $searchDateEnd;

    /**
     * 云运动 - 私教端- 教练上课量统计
     * @author 焦冰洋 <jiaobingyang@itsports.club>
     * @create 2017/8/21
     * @return string
     */
    public function getTakeNum($id,$type)
    {
        $this->getDate($type);
        $data = AboutClass::find()
            ->alias('ac')
            ->joinWith(['memberCourseOrderDetails mcod'=>function($query){
                $query->joinWith(['memberCourseOrder mco']);
            }])
            ->select(['mco.product_name','COUNT(ac.id) AS num'])
            ->where(['coach_id'=>$id,'status'=>4])
            ->andWhere(['between','ac.end',strtotime($this->searchDateStart),strtotime($this->searchDateEnd)])
            ->groupBy(['mco.product_id'])
            ->createCommand()
            ->queryAll();
        return $data;
    }

    public function getDate($attr)
    {
        switch($attr)
        {
            case $attr == 'd';
                $this->searchDateStart = Func::getTokenClassDate($attr,true);
                $this->searchDateEnd   = Func::getTokenClassDate($attr,false);
                break;
            case $attr == 'w';
                $this->searchDateStart = Func::getTokenClassDate($attr,true);
                $this->searchDateEnd   = Func::getTokenClassDate($attr,false);
                break;
            case $attr == 'm';
                $this->searchDateStart = Func::getTokenClassDate($attr,true);
                $this->searchDateEnd   = Func::getTokenClassDate($attr,false);
                break;
            case $attr == 's';
                $this->searchDateStart = Func::getTokenClassDate($attr,true);
                $this->searchDateEnd   = Func::getTokenClassDate($attr,false);
                break;
            case $attr == 'y';
                $this->searchDateStart = Func::getTokenClassDate($attr,true);
                $this->searchDateEnd   = Func::getTokenClassDate($attr,false);
                break;
        }
    }

    /**
     * 云运动 - 私教端- 教练上课量报表
     * @author 焦冰洋 <jiaobingyang@itsports.club>
     * @create 2017/8/21
     * @return string
     */
    public function getTakeList($id,$start,$end)
    {
       $data = AboutClass::find()
           ->alias('ac')
           ->joinWith(['memberCourseOrderDetails mcod'=>function($query){
               $query->joinWith(['memberCourseOrder mco']);
           }])
           ->joinWith(['member me'])
           ->joinWith('memberDetails md')
           ->select(['md.name','me.mobile','COUNT(ac.member_id) AS token_num','round(SUM(mco.money_amount/mco.course_amount),2) AS total_money'])
           ->where(['ac.coach_id'=>$id,'ac.status'=>4])
           ->andWhere(['between','ac.end',$start,$end])
           ->groupBy(['ac.member_id'])
           ->createCommand()
           ->queryAll();
        if($data){
            $sum = 0;
            foreach ($data as $v){
                $sum+=$v['token_num'];
            }
            $datas['sum']=$sum;
            $datas['data']=$data;
            return $datas;
        }else{
            return false;
        }
    }

    /**
     * 云运动 - 私教端- 教练卖课排行榜
     * @author 焦冰洋 <jiaobingyang@itsports.club>
     * @create 2017/12/16
     * @return string
     */
    public function getSellList($venueId,$type)
    {
            $query = MemberCourseOrder::find()
                ->alias('mco')
                ->joinWith(['employeeS em'])
                ->joinWith(['order o'])
                ->select(['em.name','COUNT(DISTINCT mco.member_id) AS member_number','SUM(mco.course_amount) AS course_number','SUM(mco.money_amount) AS total_money'])
                ->where(['em.venue_id'=>$venueId, 'mco.pay_status'=>1])
                ->andWhere(['>', 'mco.money_amount', 0]);
            if(in_array($type,['d','w','m','s','y'])){
                $start = strtotime(Func::getTokenClassDate($type,true));
                $end   = strtotime(Func::getTokenClassDate($type,false));
                $query->andWhere(['between','o.order_time',$start,$end]);
            }
            $data = $query
                ->groupBy('em.id')
                ->orderBy('total_money DESC')
                ->createCommand()
                ->queryAll();
            if($data){
                return $data;
            }else{
                return false;
            }
    }

    /**
     * 云运动 - 私教端- 教练业绩报表
     * @author 焦冰洋 <jiaobingyang@itsports.club>
     * @create 2017/12/16
     * @return string
     */
    public function coachAchievement($id,$start,$end)
    {
        $data = MemberCourseOrder::find()
            ->alias('mco')
            ->joinWith(['member me'])
            ->joinWith('memberDetails md')
            ->joinWith(['order o'])
            ->select(['md.name','mco.product_name','mco.course_amount','mco.money_amount','round(mco.money_amount/mco.course_amount,2) as unit_price'])
            ->where(['mco.private_id'=>$id])
            ->andWhere(['between','o.order_time',$start,$end])
            ->orderBy('mco.create_at DESC')
            ->createCommand()
            ->queryAll();
        if($data){
            $sum = 0;
            foreach ($data as $v){
                $sum+=$v['money_amount'];
            }
            $datas['sum']=number_format($sum,2);
            $datas['data']=$data;
            return $datas;
        }else{
            return false;
        }

    }



    
}