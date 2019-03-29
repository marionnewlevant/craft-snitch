<?php

namespace marionnewlevant\snitch;

use Craft;
use craft\events\ElementEvent;
use craft\services\Elements;

use marionnewlevant\snitch\assetbundles\snitch\SnitchAsset;
use marionnewlevant\snitch\models\Settings;

use yii\base\Event;


class Plugin extends \craft\base\Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Snitch::$plugin
     *
     * @var static
     */
    public static $plugin;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Snitch::$plugin
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;
        $this->setComponents([
            'collision' => \marionnewlevant\snitch\services\Collision::class,
        ]);
        if (Craft::$app->getRequest()->getIsCpRequest() && !Craft::$app->getUser()->getIsGuest() && !Craft::$app->getRequest()->getIsAjax()) {
            // Register our asset bundle
            Craft::$app->getView()->registerAssetBundle(SnitchAsset::class);
            // on save, remove any collision for this element.
            Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function(ElementEvent $event) {
                $elementId = $event->element->id;
                $this->collision->remove($elementId);
            });
        }
    }

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
