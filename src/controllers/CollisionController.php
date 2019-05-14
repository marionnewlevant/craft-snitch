<?php
/**
 * Snitch plugin for Craft CMS 3.x
 *
 * Report when two people might be editing the same entry, category, or global
 *
 * @link      http://marion.newlevant.com
 * @copyright Copyright (c) 2019 Marion Newlevant
 */

namespace marionnewlevant\snitch\controllers;

use marionnewlevant\snitch\Snitch;

use Craft;
use craft\web\Controller;

/**
 * Collision Controller
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

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['ajax-enter', 'get-config'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's actionAjaxEnter URL,
     * e.g.: actions/snitch/collision/ajax-enter
     *
     * Called from the javascript regularly (every 2 seconds)
     * to report that the thing is indeed being edited.
     *
     * @return mixed
     */
    public function actionAjaxEnter()
    {
        $this->requireAcceptsJson();

        // require login (gracefully)
        $userSession = Craft::$app->getUser();
        if ($userSession->getIsGuest()) {
            $json = $this->asJson([
                'success' => false,
                'error' => 'not logged in',
            ]);
            return $json;
        }

        $snitchId = (int)(Craft::$app->getRequest()->getBodyParam('snitchId'));
        $snitchType = Craft::$app->getRequest()->getBodyParam('snitchType');
        // expire any old collisions
        Snitch::$plugin->collision->expire();
        // record this person is editing this element
        Snitch::$plugin->collision->register($snitchId, $snitchType);
        // get any collisions
        $collisionModels = Snitch::$plugin->collision->getCollisions($snitchId, $snitchType);
        // pull the user data out of our collisions
        $userData = Snitch::$plugin->collision->userData($collisionModels);
        // and return
        $json = $this->asJson([
            'success' => true,
            'collisions' => $userData,
        ]);
        return $json;
    }

    /**
     * Handle a request going to our plugin's actionGetConfig URL,
     * e.g.: actions/snitch/collision/get-config
     *
     * @return mixed
     */
    public function actionGetConfig()
    {
        $this->requireAcceptsJson();
        $settings = Snitch::$plugin->getSettings();
        $json = $this->asJson([
            'message' => $settings['message'],
            'serverPollInterval' => $settings['serverPollInterval'],
            'elementInputIdSelector' => $settings['elementInputIdSelector'],
            'fieldInputIdSelector' => $settings['fieldInputIdSelector'],
        ]);
        return $json;
    }
}
