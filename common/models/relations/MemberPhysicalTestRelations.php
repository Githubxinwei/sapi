<?php
	/**
	 * Created by PhpStorm.
	 * User: lihuien
	 * Date: 2017/4/20
	 * Time: 13:18
	 */
	
	namespace common\models\relations;
	
	use common\models\AboutClass;
	use common\models\base\Organization;
	use common\models\base\MemberCard;
	use common\models\Member;
	use common\models\PhysicalTest;
	use Tests\Behat\Gherkin\Node\PyStringNodeTest;
	
	trait MemberPhysicalTestRelations
	{
		public function getphysicalTest()
		{
			return $this->hasOne(PhysicalTest::className(), ['id' => 'cid']);
		}
	}