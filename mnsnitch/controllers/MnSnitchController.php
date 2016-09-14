<?php
/**
 * MN Snitch plugin for Craft CMS
 *
 * MnSnitch Controller
 *
 * @author    Marion Newlevant
 * @copyright Copyright (c) 2016 Marion Newlevant
 * @link      http://marion.newlevant.com
 * @package   MnSnitch
 * @since     1.0.0
 */

namespace Craft;

class MnSnitchController extends BaseController
{

	/**
	 */
	public function actionAjaxEnter()
	{
		$this->requireAjaxRequest();
		$elementId = intval(craft()->request->getPost('elementId'));
		craft()->mnSnitch->expire();
		craft()->mnSnitch->registerCollision($elementId);
		$collisionModels = craft()->mnSnitch->getCollisions($elementId);
		$userData = craft()->mnSnitch->userData($collisionModels);
		$this->returnJson(array(
			'collisions' => $userData,
			'serverPollInterval' => craft()->config->get('serverPollInterval', 'mnsnitch'),
			'message' => craft()->config->get('message', 'mnsnitch')
		));

	}
}
