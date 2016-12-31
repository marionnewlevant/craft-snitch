<?php
/**
 * MN Snitch plugin for Craft CMS
 *
 * Detect when two people might be editing the same entry
 *
 * @author    Marion Newlevant
 * @copyright Copyright (c) 2016 Marion Newlevant
 * @link      http://marion.newlevant.com
 * @package   MnSnitch
 * @since     1.0.0
 */

namespace Craft;

class MnSnitchPlugin extends BasePlugin
{
	/**
	 * @return mixed
	 */
	public function init()
	{
		if (craft()->request->isCpRequest())
		{
			craft()->templates->includeJsResource('mnsnitch/js/mnsnitch.js');
			craft()->templates->includeCssResource('mnsnitch/css/mnsnitch.css');
			// on save entry, current user exit the entry
			craft()->on('entries.onSaveEntry', function (Event $event) {
				// Get saved entry
				$entry = $event->params['entry'];
				if ($entry) {
					craft()->mnSnitch->removeCollision($entry->id);
				}
			});
		}
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		 return Craft::t('MN Snitch');
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return Craft::t('Detect when two people might be editing the same entry, category, or global');
	}

	/**
	 * @return string
	 */
	public function getDocumentationUrl()
	{
		return 'https://github.com/https://github.com/marionnewlevant/craft-snitch/mnsnitch/blob/master/README.md';
	}

	/**
	 * @return string
	 */
	public function getReleaseFeedUrl()
	{
		return 'https://raw.githubusercontent.com/https://github.com/marionnewlevant/craft-snitch/mnsnitch/master/releases.json';
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return '1.0.2';
	}

	/**
	 * @return string
	 */
	public function getSchemaVersion()
	{
		return '1.0.0';
	}

	/**
	 * @return string
	 */
	public function getDeveloper()
	{
		return 'Marion Newlevant';
	}

	/**
	 * @return string
	 */
	public function getDeveloperUrl()
	{
		return 'http://marion.newlevant.com';
	}

	/**
	 * @return bool
	 */
	public function hasCpSection()
	{
		return false;
	}

	/**
	 */
	public function onBeforeInstall()
	{
	}

	/**
	 */
	public function onAfterInstall()
	{
	}

	/**
	 */
	public function onBeforeUninstall()
	{
	}

	/**
	 */
	public function onAfterUninstall()
	{
	}
}
