<?php
/**
 * Snitch plugin for Craft CMS 3.x
 *
 * Report when two people might be editing the same entry, category, or global
 *
 * @link      http://marion.newlevant.com
 * @copyright Copyright (c) 2017 Marion Newlevant
 */

namespace marionnewlevant\snitch\records;

use craft\db\ActiveRecord;
use DateTime;

/**
 * SnitchRecord Record
 *
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 * Active Record implements the [Active Record design pattern](http://en.wikipedia.org/wiki/Active_record).
 * The premise behind Active Record is that an individual [[ActiveRecord]] object is associated with a specific
 * row in a database table. The object's attributes are mapped to the columns of the corresponding table.
 * Referencing an Active Record attribute is equivalent to accessing the corresponding table column for that record.
 *
 * http://www.yiiframework.com/doc-2.0/guide-db-active-record.html
 *
 * @author    Marion Newlevant
 * @package   Snitch
 * @since     1.0.0
 *
 * @property int $id
 * @property int $snitchId
 * @property string|null $snitchType
 * @property int $userId
 * @property DateTime $whenEntered
 */
class SnitchRecord extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    /**
     * Declares the name of the database table associated with this AR class.
     * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
     * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is `tbl_`,
     * `Customer` becomes `tbl_customer`, and `OrderItem` becomes `tbl_order_item`. You may override this method
     * if the table is not named after this convention.
     *
     * By convention, tables created by plugins should be prefixed with the plugin
     * name and an underscore.
     *
     * @return string the table name
     */
    public static function tableName(): string
    {
        return '{{%snitch_collisions}}';
    }
}
