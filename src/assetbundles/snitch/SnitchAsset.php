<?php
/**
 */

namespace marionnewlevant\snitch\assetbundles\Snitch;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SnitchAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@marionnewlevant/snitch/assetbundles/snitch/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/snitch.js',
        ];

        $this->css = [
            'css/snitch.css',
        ];

        parent::init();
    }
}
