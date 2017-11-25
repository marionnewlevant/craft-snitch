<?php
/**
 * Snitch plugin for Craft CMS 3.x
 *
 * Report when two people might be editing the same entry, category, or global
 *
 * @link      http://marion.newlevant.com
 * @copyright Copyright (c) 2017 Marion Newlevant
 */

namespace marionnewlevant\snitch\controllers;

use marionnewlevant\snitch\Plugin as Snitch;

use Craft;
use craft\web\Controller;

/**
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Marion Newlevant
 * @package   Snitch
 * @since     1.0.0
 */
class CollisionController extends Controller
{

    public function actionAjaxEnter()
    {
        $this->requireAcceptsJson();
        $elementId = (int)(Craft::$app->getRequest()->getBodyParam('elementId'));
        // expire any old collisions
        Snitch::$plugin->collision->expire();
        // record this person is editing this element
        Snitch::$plugin->collision->register($elementId);
        // get any collisions
        $collisionModels = Snitch::$plugin->collision->getCollisions($elementId);
        // pull the user data out of our collisions
        $userData = Snitch::$plugin->collision->userData($collisionModels);
        // and return
        $json = $this->asJson([
            'collisions' => $userData,
        ]);
        return $json;
    }

    public function actionGetConfig()
    {
        $this->requireAcceptsJson();
        $settings = Snitch::$plugin->getSettings();
        $json = $this->asJson([
            'message' => $settings['message'],
            'serverPollInterval' => $settings['serverPollInterval'],
            'inputIdSelector' => $settings['inputIdSelector'],
        ]);
        return $json;
    }
}
