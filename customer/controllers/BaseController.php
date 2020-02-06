<?php
namespace customer\controllers;

use common\models\Func;
use Yii;
use common\base\AuthBaseController;

class BaseController extends AuthBaseController
{
    public $user;
    public $userId = NULL;
    public $employeeId = NULL;
    public $venueId = NULL;
    public $companyId = NULL;
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
            }
            if(!empty($this->auth = Func::getRelationVal($this->user, 'role', 'auth'))){
                Yii::$app->params['authCompanyIds'] = json_decode($this->auth->company_id, TRUE);
                Yii::$app->params['authVenueIds'] = json_decode($this->auth->venue_id, TRUE);
            }
        }
        return $action;
    }

}