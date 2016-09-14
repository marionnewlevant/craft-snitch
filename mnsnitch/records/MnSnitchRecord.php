<?php
/**
 * MN Snitch plugin for Craft CMS
 *
 * MnSnitch Record
 *
 * @author    Marion Newlevant
 * @copyright Copyright (c) 2016 Marion Newlevant
 * @link      http://marion.newlevant.com
 * @package   MnSnitch
 * @since     1.0.0
 */

namespace Craft;

class MnSnitchRecord extends BaseRecord
{
	/**
	 * @return string
	 */
	public function getTableName()
	{
		return 'mnsnitch';
	}

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
			'whenEntered' => AttributeType::DateTime,
			'elementId' => AttributeType::Number
		);
	}

	/**
	 * @return array
	 */
	public function defineRelations()
	{
		return array(
			'user' => array(static::BELONGS_TO, 'UserRecord', 'required' => true, 'onDelete' => static::CASCADE),
		);
	}
}
