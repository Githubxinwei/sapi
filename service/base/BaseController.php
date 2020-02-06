<?php
/**
 * api应用公共控制器类
 */
namespace service\base;

use service\models\User;
use Yii;
use common\models\Func;
use common\base\AuthBaseController;
use yii\web\ForbiddenHttpException;
class BaseController extends AuthBaseController
{
    public $modelClass = 'common\models\User';

    public $post = NULL;
    public $get = NULL;
    public $user = NULL;
    public $userId = NULL;
    public $coachId = NULL;
    public $venueId = NULL;
    public $companyId = NULL;
    public $postion = NULL;
    public $mobile = NULL;


    public $serializer = [
        'class' => 'service\base\MySerializer',
        'collectionEnvelope' => 'data',
    ];
    public $employeeId = NULL;

    public $auth = NULL;

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        if($this->user = Yii::$app->user->identity){
            $this->userId = Yii::$app->user->id;
            if($this->user->employee){
                $this->employeeId = $this->user->employee->id;
                $this->venueId = $this->user->employee->venue_id;
                $this->companyId = $this->user->employee->company_id;
                $this->mobile = $this->user->employee->mobile;
                $this->coachId = $this->user->employee->id;
//                $is = $this->user->employee->organization->name;
            }
            if(!empty($this->auth = Func::getRelationVal($this->user, 'role', 'auth'))){
                Yii::$app->params['authCompanyIds'] =$this->auth->company_id;
                Yii::$app->params['authVenueIds'] = $this->auth->venue_id;
                Yii::$app->params['coachId'] = $this->coachId = $this->user->employee->id;
            }
        }
        return $action;
    }
    protected function getFilterVenueId()
    {
        $venue_id = Yii::$app->request->get('venue_id', 0);
        if($venue_id && !in_array($venue_id, Yii::$app->params['authVenueIds'], TRUE)) throw new ForbiddenHttpException('无此场馆权限');
        return $venue_id ?: Yii::$app->params['authVenueIds'];
    }
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
        return [];
    }



    /**
     * model的错误信息转换成字符串以便于输出
     * @author zhangxiaobing <zhangxiaobing@itsports.club>
     * @create 2017/12/08
     * @param $errors
     * @return string
     */
    protected function toString($errors)
    {
        $str = '';
        foreach ($errors as $field => $msgs)
            foreach ($msgs as $msg)
                $str.= " $msg";
        $str = str_replace('。', '', $str);
        $str = ltrim($str, ' ');
        return $str;
    }

    /**
     * 格式化错误信息
     * @author zhangxiaobing <zhangxiaobing@itsports.club>
     * @create 2017/12/08
     * @param $msg 错误信息
     * @param array $data 数据
     * @param int $code 错误代码
     * @return array
     */
	protected function error($msg, $data=[], $code=0)
    {
        $return = ['code'=>$code, 'status'=>'error', 'message'=>$msg];
        if(!empty($data)) $return['data'] = $data;
        return $return;
    }

    /**
     * 格式化成功信息
     * @author zhangxiaobing <zhangxiaobing@itsports.club>
     * @create 2017/12/08
     * @param array $data 数据
     * @param string $msg 提示信息
     * @param int $code 代码
     * @return array
     */
    protected function success($data=[], $msg='', $code=1)
    {
        return ['code'=>$code, 'status'=>'success', 'message'=>$msg, 'data'=>$data];
    }

}