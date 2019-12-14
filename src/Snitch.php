<?php
/**
 * Snitch plugin for Craft CMS 3.x
 *
 * Report when two people might be editing the same entry, category, or global
 *
 * @link      http://marion.newlevant.com
 * @copyright Copyright (c) 2019 Marion Newlevant
 */

namespace marionnewlevant\snitch;

use marionnewlevant\snitch\services\Collision as Collision;
use marionnewlevant\snitch\assetbundles\snitch\SnitchAsset;
use marionnewlevant\snitch\models\Settings;

use Craft;
use craft\base\Plugin;
// use craft\events\ElementEvent;
// use craft\services\Elements;

// use yii\base\Event;

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
     * @var Snitch
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
     public $schemaVersion = '2.1.0';

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

        $this->setComponents([
            'collision' => Collision::class,
        ]);

        if (Craft::$app->getRequest()->getIsCpRequest() && !Craft::$app->getRequest()->getIsAjax()) {
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
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }
}
