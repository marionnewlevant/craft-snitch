<?php
/**
 * Snitch plugin for Craft CMS 3.x
 *
 * Report when two people might be editing the same entry, category, or global
 *
 * @link      http://marion.newlevant.com
 * @copyright Copyright (c) 2019 Marion Newlevant
 */

namespace marionnewlevant\snitch\models;

use marionnewlevant\snitch\Snitch;

use Craft;
use craft\base\Model;
use craft\validators\ArrayValidator;

/**
 * Snitch Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Marion Newlevant
 * @package   Snitch
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================


    const SERVERPOLLINTERVAL = 2;
    const MESSAGE = 'May also be edited by: <a href="mailto:{{user.email}}">{{user.username}}</a>.';
    const ELEMENT_INPUTIDSELECTOR = 'form input[type="hidden"][name="entryId"]' // entry forms
        .', form input[type="hidden"][name="elementId"]' // modals entry forms
        .', form input[type="hidden"][name="setId"]' // global set
        .', form input[type="hidden"][name="categoryId"]' // category
        .', form input[type="hidden"][name="userId"]' // user
        .', form input[type="hidden"][name="productId"]'; // product
    const FIELD_INPUTIDSELECTOR = 'form input[type="hidden"][name="fieldId"]';

    /**
     * Some field model attribute
     *
     * @var int
     */
    public $serverPollInterval = self::SERVERPOLLINTERVAL;

    /**
     * @var string
     */
    public $messageTemplate = self::MESSAGE;

    /**
     * @var string
     */
    public $elementInputIdSelector = self::ELEMENT_INPUTIDSELECTOR;

    /**
     * @var string
     */
    public $fieldInputIdSelector = self::FIELD_INPUTIDSELECTOR;


    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $myRules = [
            ['serverPollInterval', 'integer', 'min' => 1, 'max' => 5],
            ['serverPollInterval', 'default', 'value' => self::SERVERPOLLINTERVAL],
            ['messageTemplate', 'string'],
            ['messageTemplate', 'default', 'value' => self::MESSAGE],
            ['elementInputIdSelector', 'string'],
            ['fieldInputIdSelector', 'string'],
        ];
        $rules = array_merge($rules, $myRules);

        return $rules;
    }
}
