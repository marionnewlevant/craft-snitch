<?php
/**
 * MN Snitch plugin for Craft CMS
 *
 * MnSnitch Model
 *
 * @author    Marion Newlevant
 * @copyright Copyright (c) 2016 Marion Newlevant
 * @link      http://marion.newlevant.com
 * @package   MnSnitch
 * @since     1.0.0
 */

namespace Craft;

class MnSnitchModel extends BaseModel
{
	/**
	 * Define model attributes. These just happen to match the actual record fields
	 *
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array_merge(parent::defineAttributes(), array(
			'id' => AttributeType::Number,
			'whenEntered' => AttributeType::DateTime,
			'userId' => AttributeType::Number,
			'elementId' => AttributeType::Number,
			'dateCreated' => AttributeType::DateTime,
			'dateUpdated' => AttributeType::DateTime,
		));
	}

}
