<?php
namespace business\controllers;

use common\models\Func;
use Yii;
use common\base\AuthBaseController;
use yii\web\ForbiddenHttpException;

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
                Yii::$app->params['authCompanyIds'] = $this->auth->company_id;
                Yii::$app->params['authVenueIds'] = $this->auth->venue_id;
            }
        }
        return $action;
    }

    /**
     * 筛选场馆
     * @return int or array
     * @throws ForbiddenHttpException
     */
    protected function getFilterVenueId()
    {
        $venue_id = Yii::$app->request->get('venue_id', 0);
        if($venue_id && !in_array($venue_id, Yii::$app->params['authVenueIds'], TRUE)) throw new ForbiddenHttpException('无此场馆权限');
        return $venue_id ?: Yii::$app->params['authVenueIds'];
    }

}