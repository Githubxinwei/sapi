<?php
namespace group\controllers;

use common\models\Func;
use coach\models\User;
use Yii;
use common\base\AuthBaseController;

class BaseController extends AuthBaseController
{
    public $userId = NULL;
    public $employeeId = NULL;
    public $venueId = NULL;
    public $companyId = NULL;
    public $auth = NULL;
    public $doorArr = array();
    public $language;
    public function beforeAction($action)
    {
        parent::beforeAction($action);
        $language = \Yii::$app->request->get('language', 'zh-CN');
        if ($language !== 'en-us') {
            $this->language = 'zh-CN';
        } else {
            $this->language = 'en-us';
        }
        \Yii::$app->language = $this->language;
        if($this->user = Yii::$app->user->identity){
            $this->userId = Yii::$app->user->id;
            if($this->user->employee){
                $this->employeeId = $this->user->employee->id;
                $this->venueId = $this->user->employee->venue_id;
                $this->companyId = $this->user->employee->company_id;
            }
            if(!empty($this->auth = Func::getRelationVal($this->user, 'role', 'auth'))){
                Yii::$app->params['authCompanyIds'] = $this->auth->company_id;
                Yii::$app->params['authVenueIds'] = $this->auth->venue_id;
            }
        }
        return $action;
    }
}