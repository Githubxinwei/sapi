<?php
namespace customer\models;

use common\models\Member;
use common\models\MemberAccount;
use common\models\Organization;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class LoginForm extends Model
{
    public $mobile;
    public $password;
    public $company_id;
    private $_user;

    public function rules()
    {
        return [
            [['mobile', 'password', 'company_id'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'mobile'     => '手机号码',
            'password'   => '密码',
            'company_id' => '公司代号',
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $users = User::find()->where(['mobile'=>$this->mobile, 'company_id'=>$this->company_id])->limit(2)->all();
            if(count($users) == 2){
                $this->addError($attribute, '账户异常');
                return FALSE;
            }
            if (!isset($users[0]) || !$users[0]->validatePassword($this->password)) {
                $this->addError($attribute, '手机号码或密码错误.');
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            Yii::$app->user->login($this->getUser());
            Yii::$app->user->identity->updateAccessToken();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['mobile'=>$this->mobile, 'company_id'=>$this->company_id]);
        }

        return $this->_user;
    }

    /**
     * 登录成功后返回客户端的会员信息
     * @return array
     */
    public function info()
    {
        $venue_ids = ArrayHelper::getColumn(Member::find()->select('venue_id')->where(['member_account_id'=>$this->_user->id])->asArray()->all(), 'venue_id');
        return [
            'accesstoken' => $this->_user->accesstoken,
            'venues' => Organization::find()->select('id,name')->where(['id'=>$venue_ids])->all(),
        ];
    }
}