<?php
/**
 * MN Snitch plugin for Craft CMS
 *
 * MnSnitch Service
 *
 * @author    Marion Newlevant
 * @copyright Copyright (c) 2016 Marion Newlevant
 * @link      http://marion.newlevant.com
 * @package   MnSnitch
 * @since     1.0.0
 */

namespace Craft;

class MnSnitchService extends BaseApplicationComponent
{
	/**
	 */
	public function registerCollision($elementId, $userId = null, $now = null)
	{
		$now = $this->_now($now);
		$userId = $this->_userId($userId);
		// look for existing record to update
		$record = MnSnitchRecord::model()->findByAttributes(array(
			'elementId' => $elementId,
			'userId' => $userId
		));

		if (!$record)
		{
			$record = new MnSnitchRecord();
			$record->userId = $userId;
			$record->elementId = $elementId;
		}
		$record->whenEntered = $now;
		$result = $record->save();
	}

	public function getCollisions($elementId, $userId = null, $now = null)
	{
		$result = array();
		$now = $this->_now($now);
		$userId = $this->_userId($userId);
		$rows = MnSnitchRecord::model()->findAll('elementId=:elementId', array(':elementId'=>$elementId));
		foreach ($rows as $row) {
			$result[] = MnSnitchModel::populateModel($row);
		}
		return $result;
	}

	public function removeCollision($elementId, $userId = null)
	{
		$userId = $this->_userId($userId);
		$record = MnSnitchRecord::model()->findByAttributes(array(
			'elementId' => $elementId,
			'userId' => $userId
		));
		if ($record) {
			$record->delete();
		}
	}

	public function expire($now = null)
	{
		$now = $this->_now($now);
		$timeOut = craft()->config->get('serverPollInterval', 'mnnocollide') * 10;
		$old = clone $now;
		$old->sub(new DateInterval('PT'.$timeOut.'S'));
		$oldDateString = DateTimeHelper::formatTimeForDb($old);
		$x = MnSnitchRecord::model()->deleteAll('whenEntered<:when', array(':when'=>$oldDateString));
	}

	public function userData(array $collisionModels, $userId = null)
	{
		$userId = $this->_userId($userId);
		$result = array();
		$ids = array();
		foreach ($collisionModels as $model) {
			$ids[] = $model->userId;
		}
		$ids = array_unique($ids);

		foreach ($ids as $id) {
			if ($id !== $userId)
			{
				$user = craft()->users->getUserById($id);
				if ($user) {
					$result[] = array(
						'name' => $user->friendlyName,
						'email' => $user->email
					);
				}
			}
		}
		return $result;
	}

	// ============== default values =============
	private function _userId($userId)
	{
		if (!$userId) {
			$currentUser = craft()->userSession->getUser();
			if ($currentUser) {
				$userId = $currentUser->id;
			}
		}
		return $userId;
	}

	private function _now($now)
	{
		return ($now ? $now : DateTimeHelper::currentUTCDateTime());
	}
}
