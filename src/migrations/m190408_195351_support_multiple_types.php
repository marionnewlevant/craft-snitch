<?php

namespace marionnewlevant\snitch\migrations;

use Craft;
use craft\db\Migration;

/**
 * m190408_195351_support_multiple_types migration.
 */
class m190408_195351_support_multiple_types extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropTableIfExists('{{%snitch_collisions}}');

        // create our table
        $this->createTable('{{%snitch_collisions}}', [
            'id' => $this->primaryKey(),
            'snitchId' => $this->integer()->notNull(),
            'snitchType' => $this->string(),
            'userId' => $this->integer()->notNull(),
            'whenEntered' => $this->dateTime()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        // foreign keys: our userId must be a user id
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%snitch_collisions}}', 'userId'),
            '{{%snitch_collisions}}', 'userId', '{{%users}}', 'id', 'CASCADE', null);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists('{{%snitch_collisions}}');
    }
}
