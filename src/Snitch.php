<?php
/**
 * Snitch plugin for Craft CMS
 *
 * Report when two people might be editing the same entry, category, or global
 *
 * @link      http://marion.newlevant.com
 * @copyright Copyright (c) 2019 Marion Newlevant
 */

namespace marionnewlevant\snitch;

use craft\services\Plugins;
use marionnewlevant\snitch\services\Collision as Collision;
use marionnewlevant\snitch\assetbundles\snitch\SnitchAsset;
use marionnewlevant\snitch\models\Settings;

use Craft;
use craft\base\Plugin;
use yii\base\Event;


/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Marion Newlevant
 * @package   Snitch
 * @since     1.0.0
 *
 * @property  Collision $collision
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class Snitch extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Snitch::$plugin
     *
     * @var Snitch|null
     */
    public static ?Snitch $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
     public string $schemaVersion = '2.1.0';

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Snitch::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $request = Craft::$app->getRequest();

        if (!$this->isInstalled || $request->getIsConsoleRequest()) {
            return;
        }

        $this->setComponents([
            'collision' => Collision::class,
        ]);

        if ($request->getIsCpRequest()
            && !$request->getIsAjax()
            && !Craft::$app->getUser()->getIsGuest()
        ) {
            // delay this check until plugins loaded...
            Event::on(Plugins::class, Plugins::EVENT_AFTER_LOAD_PLUGINS, function () {
                $user = Craft::$app->getUser();
                // TwoFactorAuth - either not there, or not turned on for this user, or we have passed that hurdle
                if (!Craft::$app->plugins->isPluginInstalled('two-factor-authentication')
                    || !\born05\twofactorauthentication\Plugin::$plugin->verify->isEnabled($user->getIdentity())
                    || \born05\twofactorauthentication\Plugin::$plugin->verify->isVerified($user->getIdentity())
                )
                {
                    // Register our asset bundle
                    Craft::$app->getView()->registerAssetBundle(SnitchAsset::class);
                    // on save, remove any collision for this element.
                    // I used to do this, but it ties me to
                    // specific classes. Instead, just rely on the record timing out.
                    // Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function(ElementEvent $event) {
                    //     $elementId = $event->element->id;
                    //     $this->collision->remove($elementId, 'element');
                    // });
                }
            });
        }

        Craft::info(
            Craft::t(
                'snitch',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return Settings
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }
}
