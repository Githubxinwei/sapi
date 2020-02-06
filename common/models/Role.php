<?php

namespace common\models;

use common\models\relations\RoleRelations;

class Role extends \common\models\base\Role
{
    use RoleRelations;
}