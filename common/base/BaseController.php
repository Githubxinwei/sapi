<?php
/**
 * 应用公共控制器类
 */
namespace common\base;

use Yii;
use yii\web\NotFoundHttpException;

abstract class BaseController extends \yii\rest\ActiveController
{
    public $modelClass = 'common\models\User';

    public $post = NULL;
    public $get = NULL;

    public $serializer = [
        'class' => '\common\libs\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors'  => ['Origin' => ['*']],
        ];
        $behaviors['contentNegotiator']['formats'] = ['application/json' => yii\web\Response::FORMAT_JSON];
        return $behaviors;
    }

    public function actions()
    {
        if($this->modelClass == 'common\models\User'){
            return [
                'version' => [
                    'class' => 'common\libs\VersionAction',
                ],
            ];
        }
        return parent::actions();
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH', 'POST'],
            'delete' => ['DELETE', 'GET'],
        ];
    }

    public function beforeAction($action)
    {
        parent::beforeAction($action);

        $this->post = Yii::$app->request->post();
        $this->get = Yii::$app->request->get();

        return $action;
    }

    /**
     * 返回成功信息
     * @author zhangxiaobing <zhangxiaobing@itsports.club>
     * @create 2017/12/08
     * @param $msg 成功信息
     * @return array
     */
    protected function success($msg)
    {
        return ['message'=>$msg];
    }

    /**
     * 抛出异常
     * @author zhangxiaobing <zhangxiaobing@itsports.club>
     * @create 2017/12/08
     * @param $msg 错误信息
     * @return array
     */
	protected function error($msg)
    {
        throw new NotFoundHttpException($msg);
    }
    /**
     * 返回错误信息
     * @author 李慧恩 <lihuien@itsports.club>
     * @create 2017/12/08
     * @param $msg 错误信息
     * @return array
     */
    protected function returnError($msg)
    {
        return   ['message'=>$msg, 'code'=>0, 'status'=>'422'];
    }

    /**
     * 返回表单模型错误信息
     * @author zhangxiaobing <zhangxiaobing@itsports.club>
     * @create 2017/12/23
     * @param $errors
     * @return array
     */
    protected function modelError($errors)
    {
        Yii::$app->response->statusCode = 422;
        $info = [];
        foreach ($errors as $field => $msgs)
            $info[] = ['field'=>$field, 'message'=>$msgs[0]];
        return $info;
    }

}