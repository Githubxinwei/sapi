<?php

namespace common\models;
use common\models\relations\EmployeeRelations;
use Yii;

class Employee extends \common\models\base\Employee
{
    use EmployeeRelations;

}