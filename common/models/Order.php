<?php
namespace common\models;


class Order extends \common\models\base\Order
{
      use \common\models\relations\OrderRelations;

      public function getProduct_name()
      {
          switch ($this->consumption_type){
              case 'card':
                  return Func::getRelationVal($this, 'memberCard', 'card_name');
                  break;
              case 'charge':
                  return Func::getRelationVal($this, 'memberCourseOrder', 'chargeClass', 'name');
                  break;
              case 'chargeGroup':
                  return Func::getRelationVal($this, 'memberCourseOrder', 'chargeClass', 'name');
                  break;
          }
          return '';
      }

    /**
     * 销售额统计
     * @param $params ['start'=>1517587200, 'end'=>1517587200, 'venue_id'=>[1,2]]
     * @return int
     */
      public static function saleSum($params)
      {
          $query = static::find()->where(['status'=>2])->andWhere(['<>','note','回款']);

          if(isset($params['start']) && isset($params['end'])) $query->andWhere(['between', 'pay_money_time', $params['start'], $params['end']]);
          if(isset($params['venue_id'])) $query->andWhere(['venue_id'=>$params['venue_id']]);

          return $query->sum('total_price') ?: 0;
      }

    /**
     * 销售排行榜
     * @param $params ['start'=>1517587200, 'end'=>1517587200, 'venue_id'=>[1,2]]
     * @return array
     */
      public static function saleRank($params)
      {
          $query = static::find()->alias('o')->select('o.sell_people_name as name, sum(o.total_price) as sum')
              ->joinWith('employeeS e')->where(['<>', 'e.status', 2])
              ->andWhere(['o.status'=>2])
              ->andWhere(['not', ['o.sell_people_name'=>NULL]]);

          if(isset($params['start']) && isset($params['end'])) $query->andWhere(['between', 'o.pay_money_time', $params['start'], $params['end']]);
          if(isset($params['venue_id'])) $query->andWhere(['o.venue_id'=>$params['venue_id']]);

          return $query->groupBy('o.sell_people_id')->orderBy('sum desc')->createCommand()->queryAll();
      }
}