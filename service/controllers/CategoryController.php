<?php
namespace service\controllers;

use service\base\BaseController;
use service\models\Category;
use service\models\Order;
use common\models\Func;
use Yii;
use common\libs\ActiveDataProvider;


class CategoryController extends BaseController
{
    public $modelClass = 'service\models\Category';
    /**
     * @api {get} /service/category/table  卡种收入统计报表
     * @apiName        1卡种收入统计报表
     * @apiGroup       category
     * @apiParam  {string}            date              日期(2018-01-11)
     * @apiParam  {string}            venue_id          场馆ID
     * @apiParam  {string}            behavior          交费行为(接口：/business/category/behaviors)
     * @apiParam  {int}               pay_money_mode    付款方式：1现金；2支付宝；3微信；4pos刷卡；
     * @apiParam  {string}            page              页码（可选，默认1）
     * @apiParam  {string}            per-page          每页显示数（可选，默认20）
     * @apiDescription   卡种收入统计报表
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/category/table
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "items": [
                {
                    "id": "478",
                    "venue_name": "艾搏尊爵汇馆",
                    "member_name": "15537312038",
                    "note": "售卡",
                    "product_name": "一年白金瑜伽卡",
                    "total_price": "4099.00",
                    "employee_name": "",
                    "single": "",
                    "pay_money_time": "1504084791"
                },
                {
                    "id": "490",
                    "venue_name": "艾搏尊爵汇馆",
                    "member_name": "15537312038",
                    "note": "售卡",
                    "product_name": "一年白金瑜伽卡",
                    "total_price": "4099.00",
                    "employee_name": "",
                    "single": "",
                    "pay_money_time": "1504163016"
                },
            ],
            "_links": {
                "self": {
                    "href": "http://127.0.0.3/business/category/table?accesstoken=000_1544587932&page=1"
                }
            },
            "_meta": {
                "totalCount": 20,
                "pageCount": 1,
                "currentPage": 1,
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
    public function actionTable()
    {
        $date = Yii::$app->request->get('date', 0);
        $venue = Yii::$app->request->get('venue_id', 0);
        $venue_id = $venue ? $venue:$this->venueId;
        $behavior = Yii::$app->request->get('behavior', 0);
        $pay_money_mode = Yii::$app->request->get('pay_money_mode', 0);

        $query = Order::find()->where(['venue_id'=>$venue_id, 'status'=>2, 'note'=>['售卡', '续费', '升级', '办卡', '迈步一体机售卡','回款']]);

        if($date){
            $start = strtotime($date);
            $end = strtotime($date.' 23:59:59');
            $query->andWhere(['between', 'pay_money_time', $start, $end]);
        }
        if($behavior) $query->andWhere(['note'=>$behavior]);
        if($pay_money_mode) $query->andWhere(['pay_money_mode'=>$pay_money_mode]);

        $type = Yii::$app->request->get('type', 0);
        if(in_array($type, ['d', 'w', 'm', 's', 'y'])){
            $start = strtotime(Func::getTokenClassDate($type, TRUE));
            $end = strtotime(Func::getTokenClassDate($type, FALSE));
            $query->andWhere(['between', 'pay_money_time', $start, $end]);
        }

        $querySum = clone $query;

        $query->orderBy('pay_money_time desc');
        return new ActiveDataProvider(['query' => $query, 'extra'=>['sum'=>$querySum->sum('total_price')]]);
    }

     /**
     * @api {get} /service/category/behaviors  交费行为列表
     * @apiName        9交费行为列表
     * @apiGroup       category
     * @apiDescription   交费行为列表
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/12
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/category/behaviors
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": [
            "会员卡转卡",
            "办卡",
            "升级",
            "售卡",
            "购卡"
        ]
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "",
        "code": 0,
        "status": 422,
        "data": []
    }
     */
    public function actionBehaviors()
    {
        $data = Order::find()->select('note')->where(['venue_id'=>Yii::$app->params['authVenueIds']])->andWhere(['consumption_type'=>'card'])->groupBy('note')->all();
        return ArrayHelper::getColumn($data,'note');
    }

    /**
     * @api {get} /service/category/details  卡种详情
     * @apiName        4卡种详情
     * @apiGroup       category
     * @apiDescription   卡种详情
     * <br/>
     * <span><strong>作    者：</strong></span>王亮亮<br/>
     * <span><strong>邮    箱：</strong></span>wangliangliang@itsports.club
     * <span><strong>创建时间：</strong></span>2018/01/17
     * @apiSampleRequest  http://apiqa.aixingfu.net/service/category/details
     * @apiSuccess (返回值) {json} data
     * @apiSuccessExample {json}返回值详情（成功）

    {
        "message": "",
        "code": 1,
        "status": 200,
        "data": {
            "id": "1553",
            "category_type_id": "1",
            "card_name": "大店通卡T12MD",//名称
            "create_at": "1512547064",
            "class_server_id": null,
            "server_combo_id": null,
            "times": "0",
            "count_method": "0",
            "sell_start_time": null,
            "sell_end_time": null,
            "attributes": 1,
            "total_store_times": -1,
            "venue_id": "56",
            "payment": 1,
            "payment_months": 0,
            "total_circulation": "-1",
            "sex": -1,
            "age": -1,
            "transfer_number": 1,
            "create_id": "24",
            "regular_renew_time": null,
            "regular_transform_time": null,
            "original_price": null,//原价
            "sell_price": null,//售价
            "max_price": "4087.00",//售价区间大
            "min_price": "3587.00",//售价区间小
            "sales_mode": "1",
            "missed_times": -1,
            "limit_times": -1,
            "active_time": "1",//激活期限（天）
            "status": 1,
            "transfer_price": "300.00",
            "leave_total_days": "0",
            "leave_total_times": null,
            "leave_long_limit": "null",
            "recharge_price": null,
            "recharge_give_price": null,
            "single_price": null,
            "recharge_start_time": null,
            "recharge_end_time": null,
            "person_times": null,
            "service_pay_ids": null,
            "renew_price": null,
            "leave_least_Days": 0,
            "duration": "{\"day\": 365}",//有效期
            "deal_id": "69",
            "another_name": "",
            "offer_price": null,//优惠价
            "renew_unit": 30,
            "company_id": "49",
            "single": 2,
            "is_app_show": 2,
            "bring": 0,
            "card_type": 1,//卡种类型，1:瑜伽,2:健身,3舞蹈,4:综合
            "ordinary_renewal": "0.00",//普通续费
            "validity_renewal": "null",
            "pic": null,//卡图片
            "deal": {
                "id": "69",
                "name": "迈步大店通卡年卡（含团课）会员权益",
                "deal_type_id": "13",
                "create_at": "1509701897",
                "create_id": "110",
                "deal_number": "sp150970189714728",
                "company_id": "49",
                "venue_id": "56",
                "intro": "<p class=\"MsoNormal\" style=\"margin-right:0.0000pt;mso-para-margin-right:0.0000gd;text-autospace:ideograph-numeric;mso-pagination:none;line-height:150%;\"><b><span style=\"font-family: 宋体; line-height: 150%; color: rgb(0, 0, 0); font-size: 12pt;\"><font face=\"宋体\">迈步</font></span></b><b><span style=\"font-family: 宋体; line-height: 150%; color</font></span><span style=\"mso-spacerun:'yes';font-family:宋体;color:rgb(0,0,0);font-size:12.0000pt;mso-font-kerning:0.0000pt;\"><font face=\"宋体\">服务</font></span><span style=\"mso-spacerun:'yes';font-family:宋体;color:rgb(0,0,0);font-size:12.0000pt;mso-font-kerning:0.0000pt;\"><font face=\"宋体\">：每月可通用我爱运动花丹店</font></span><span style=\"mso-spacerun:'yes';font-family:宋体;color:rgb(0,0,0);font-size:12.0000pt;mso-font-kerning:0.0000pt;\">4</span><span style=\"mso-spacerun:'yes';font-family:宋体;color:rgb(0,0,0);font-size:12.0000pt;mso-font-kerning:0.0000pt;\"><font face=\"宋体\">次；每月可通用我爱运动丰庆店不限次数，不限节数。</font></span></p>",
                "type": 1
            },
            "sell_limit_card_number": [//售卖场馆
                {
                    "id": "2948",
                    "card_category_id": "1553",
                    "venue_id": "56",
                    "times": null,
                    "limit": -1,//卡限发量（-1代表不限）
                    "level": 0,
                    "status": 2,
                    "sell_start_time": "1512057600",//售卖开始时间
                    "sell_end_time": "1882972799",//售卖结束时间
                    "surplus": -1,
                    "week_times": null,
                    "venue_ids": null,
                    "identity": null,
                    "apply_start": null,
                    "apply_end": null,
                    "venue_name": [
                        "天伦锦城店"//场馆名称
                    ],
                    "discount": [
                        {
                            "discount": 8,//折扣
                            "surplus": 55//折扣张数 -1为不限
                        },
                        {
                            "discount": 7,
                            "surplus": 7
                        }
                    ]
                }
            ],
            "apply_limit_card_number": [//通用场馆
                {
                    "id": "2949",
                    "card_category_id": "1553",
                    "venue_id": "0",
                    "times": null,//月限制次数 -1为不限
                    "limit": null,
                    "level": 1,//卡等级1普通2VIP
                    "status": 1,
                    "sell_start_time": null,
                    "sell_end_time": null,
                    "surplus": null,
                    "week_times": -1,//周限制次数 -1为不限
                    "venue_ids": "[\"12\", \"56\"]",
                    "identity": null,
                    "apply_start": null,
                    "apply_end": null,
                    "venue_name": [
                        "丰庆路游泳健身馆",
                        "天伦锦城店"
                    ]
                },
                {
                    "id": "2950",
                    "card_category_id": "1553",
                    "venue_id": "59",
                    "times": 4,
                    "limit": null,
                    "level": 1,
                    "status": 1,
                    "sell_start_time": null,
                    "sell_end_time": null,
                    "surplus": null,
                    "week_times": null,
                    "venue_ids": null,
                    "identity": null,
                    "apply_start": null,
                    "apply_end": null,
                    "venue_name": [
                        "花园路丹尼斯店（尊爵汇）"
                    ]
                }
            ],
            "bind_group": [//团课套餐
                {
                    "id": "2565",
                    "card_category_id": "1553",
                    "polymorphic_id": "0",
                    "polymorphic_type": "class",
                    "number": -1,//每日节数 -1为不限
                    "status": 1,
                    "company_id": null,
                    "venue_id": null,
                    "polymorphic_ids": "[\"1\", \"2\", \"3\"]",
                    "course_name": [//课程名称
                        "瑜伽",
                        "舞蹈",
                        "健身"
                    ]
                }
            ],
            "bind_private": [//绑定私教课
                {
                    "id": "2566",
                    "card_category_id": "1553",
                    "polymorphic_id": "15",
                    "polymorphic_type": "hs",//私课类型
                    "number": 2,//私课节数
                    "status": 4,
                    "company_id": null,
                    "venue_id": null,
                    "polymorphic_ids": null,
                    "course_name": "PT常规课"//课程名称
                }
            ]
        }
    }
     * @apiSuccessExample {json}返回值详情（失败）
    {
        "message": "Object not found: 3",
        "code": 0,
        "status": 404
    }
     */
    public function actionDetails(){
        $id = Yii::$app->request->get('id','0');
        if (empty($id)) return $this->error('请选择要查看详情!');
        $model = Category::findOne($id);
        return $model;
    }

}
