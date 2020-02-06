<?php

namespace common\models;

use common\models\relations\AboutClassRelations;
use function GuzzleHttp\Psr7\str;
use yii\base\InvalidParamException;

class AboutClass extends \common\models\base\AboutClass
{
      use AboutClassRelations;

    /**
     * 本月取消次数
     */
      public function getMonthCancel()
      {
          $from = strtotime(date('Y-m-1'));
          $to = strtotime(date('Y-m-1',strtotime('+1 month')));
          return static::find()->where(['member_id'=>$this->member_id])->andWhere(['between', 'cancel_time', $from, $to])->count();
      }

    /**
     * 会员上课统计
     * @param $params ['company_id'=>1,'start'=>'2017-01-01','end'=>'2018-01-01','venue_id'=>[75,76], 'class'=>1, 'status'=>[2,3]] class人次0 节次1
     * @return array
     */
      public static function classCount($params)
      {
          if(!isset($params['company_id'])) throw new InvalidParamException('company_id can not be blank');
          //团课
          $topCourses = Course::find()->select('id,name')->where(['pid'=>0, 'class_type'=>2, 'company_id'=>$params['company_id']])->all();
          if(empty($topCourses)) return [];

          $data = [];
          foreach ($topCourses as $topCourse){
              $query = AboutClass::find()->alias('ac')
                  ->joinWith(['groupClass gc'=>function($q){
                      $q->joinWith('course c');
                  }])
                  ->where(['ac.type'=>2]);
              if(isset($params['status'])) $query->andWhere(['ac.status'=>$params['status']]);
              if(isset($params['venue_id'])) $query->andWhere(['gc.venue_id'=>$params['venue_id']]);
              if(isset($params['start']) && isset($params['end'])){
                  $query->andWhere(['between', 'ac.class_date', $params['start'], $params['end']]);
              }
              $query->andWhere(['or', ['like', 'c.path', ",{$topCourse->id},"],  ['like', 'c.path', ",{$topCourse->id}\""]]);
              if(isset($params['class']) && $params['class']==1) $query->groupBy('gc.id');//节次
              $data[] = ['name'=>$topCourse->name, 'count'=>$query->count()];
          }

          //私课
          $query = AboutClass::find()->alias('ac')
              ->joinWith('employee e')
              ->where(['ac.type'=>1]);
          if(isset($params['status'])) $query->andWhere(['ac.status'=>$params['status']]);
          if(isset($params['venue_id'])) $query->andWhere(['e.venue_id'=>$params['venue_id']]);
          if(isset($params['start']) && isset($params['end'])){
              $query->andWhere(['between', 'ac.class_date', $params['start'], $params['end']]);
          }
          $data[] = ['name'=>'私课', 'count'=>$query->count()];
          return $data;
      }

    public function fields()
    {
        return [
          'id',
            'start' => function($model)
            {
              if($model->status == 1)
              {
                return date('m月d日 H:i',$model->create_at);
              }
              if($model->status==2)
              {
                if(empty($model->cancel_time))
                {
                  return "暂无";
                }
                return date('m月d日 H:i',$model->cancel_time);

              }
            },
            'status',

            'content' => function($model)
            {
              if($model->status==1)
              {
                return Func::getRelationVal($model,'memberDetails','name').'预约了'.date('m月d日 H:i',$model->start).'的'.Func::getRelationVal($model, 'memberCourseOrderDetails', 'product_name');
              }
              if($model->status==2)
              {
                return Func::getRelationVal($model,'memberDetails','name').'取消了'.date('m月d日 H:i',$model->start).'的'.Func::getRelationVal($model, 'memberCourseOrderDetails', 'product_name');
              }

                
            },
	        'class_date',
	        'start'

        ];
    }




}