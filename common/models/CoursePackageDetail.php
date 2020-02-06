<?php

namespace common\models;
use Yii;

use \common\models\relations\CoursePackageDetailRelations;

class CoursePackageDetail extends \common\models\base\CoursePackageDetail
{
	use CoursePackageDetailRelations;
}