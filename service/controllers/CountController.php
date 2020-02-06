<?php
namespace service\controllers;

use service\models\AboutClass;
use service\models\EntryRecord;
use service\models\Order;
use common\models\Feedback;
use common\models\Func;
use common\models\GroupClass;
use service\base\BaseController;
use Yii;

class CountController extends BaseController
{
    /**
     * @api {get} /service/count/index  统计
     * @apiName        1统计
     * @apiGroup       count
     * @apiParam  {string}            type       日期类别：d 日、w 周 、m 月 、s 季度 、y 年
     * @apiParam  {string}            venue_id   场馆ID
     * @apiDescription   统计
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/02/09
     * @apiSampleRequest  http://apiqa.aixingfu.net/business/count/index
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）
    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "entry_count": "4835",//到店会员统计
            "sale_count": "700037.03",//销售总额
            "private_count": "736",//私教上课量
            "feedback_count": "1",//场馆投诉量
            "group_rank": [//团课上课排行
                {
                    "name": "基础瑜伽",//课程名称
                    "member_num": "0",//上课人数
                    "class_num": "2"//排课节数
                },
                {
                    "name": "和缓瑜伽",
                    "member_num": "0",
                    "class_num": "2"
                },
                {
                    "name": "哈他瑜伽",
                    "member_num": "320",
                    "class_num": "76"
                },
                {
                    "name": "体位呼吸法",
                    "member_num": "18",
                    "class_num": "8"
                },
            ]
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

    public function actionIndex()
    {
        $start = Yii::$app->request->get('start', 0);
        $end = Yii::$app->request->get('end', 0);
        $venue_id = Yii::$app->request->get('venue_id', Yii::$app->params['authVenueIds']);
        $type  = Yii::$app->request->get('type', 0);
        if(in_array($type, ['d', 'w', 'm', 's', 'y'], TRUE)){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
        }
//        return $end;

        $data['entry_count'] = EntryRecord::find()->where(['venue_id'=>$venue_id])->andWhere(['between', 'entry_time', $start, $end])->count();
        $data['sale_count'] = number_format(Order::saleSum(compact('start','end','venue_id')), 2, '.', '');
        $data['private_count'] = AboutClass::find()->andWhere(['type'=>1, 'e.venue_id'=>$venue_id, 'ac.status'=>[3,4]])->andWhere(['between', 'start', $start, $end])->count();
        $data['feedback_count'] = Feedback::find()->where(['venue_id'=>$venue_id])->andWhere(['between', 'created_at', $start, $end])->count();
//        $data['feedback_count'] = '0';//此版本暂不推投诉量

//        $courses = GroupClass::find()->select('gc.course_id, c.name, count(gc.id) as count')->alias('gc')
//            ->joinWith('course c')
//            ->where(['gc.venue_id'=>$venue_id])
//            ->andWhere(['between', 'gc.class_date', date('Y-m-d',$start), date('Y-m-d',$end)])
//            ->groupBy('gc.course_id')->createCommand()->queryAll();
//        $group_rank = [];
//        foreach ($courses as $course)
//        {
//            $group_rank[] = [
//                'name' => $course['name'],
//                'member_num' => \common\models\AboutClass::find()->alias('ac')->joinWith('groupClass gc')
//                    ->where(['gc.venue_id'=>$venue_id, 'gc.course_id'=>$course['course_id']])
//                    ->andWhere(['<>', 'ac.status', 2])
//                    ->andWhere(['between', 'gc.class_date', date('Y-m-d',$start), date('Y-m-d',$end)])
//                    ->count(),
//                'class_num'=>$course['count'],
//            ];
//        }
//        array_multisort(array_column($group_rank, 'member_num'), SORT_DESC, $group_rank);
//        $data['group_rank'] = $group_rank;
        return $data;
    }

}
