<?php
/**
 * 需要权限验证的公共控制器类
 */
namespace coach\base;

use coach\models\User;
use Yii;

abstract class AuthBaseController extends BaseController
{

    public function beforeAction($action)
    {
        parent::beforeAction($action);

        $accesstoken = Yii::$app->request->get('accesstoken', '');
        $user = User::findIdentityByAccessToken($accesstoken);
        if(empty($accesstoken) || !$user){
            echo json_encode($this->error('accesstoken错误', '', -1));
            exit;
        }

        if(!$user->isAccessTokenValid()){
            echo json_encode($this->error('accesstoken已过期', '', -1));
            exit;
        }

        Yii::$app->user->login($user);
        $this->user = Yii::$app->user->identity;
        $this->userId = Yii::$app->user->id;
        Yii::$app->params['coachId'] = $this->coachId = $this->user->employee->id;
        $this->venueId = $this->user->employee->venue_id;
        $this->companyId = $this->user->employee->company_id;
        return $action;
    }

}