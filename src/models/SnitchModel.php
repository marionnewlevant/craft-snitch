<?php
/**
 * Snitch plugin for Craft CMS
 *
 * Report when two people might be editing the same entry, category, or global
 *
 * @link      http://marion.newlevant.com
 * @copyright Copyright (c) 2019 Marion Newlevant
 */

namespace marionnewlevant\snitch\models;

use craft\base\Model;
use craft\validators\DateTimeValidator;
use DateTime;

/**
 * SnitchModel Model
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
class SnitchModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Model attributes. These match the fields in snitch_collisions, as defined
     * in migrations/Install
     */
    public int $id;
    public int $snitchId;
    public ?string $snitchType;
    public int $userId;
    public DateTime $whenEntered;
    public DateTime $dateCreated;
    public DateTime $dateUpdated;
    public string $uid;

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
    public function rules(): array
    {
        return [
            [['id', 'snitchId', 'userId'], 'number', 'integerOnly' => true],
            [['whenEntered', 'dateCreated', 'dateUpdated'], DateTimeValidator::class],
        ];
    }
}
