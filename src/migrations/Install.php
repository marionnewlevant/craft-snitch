<?php
namespace marionnewlevant\snitch\migrations;

use craft\db\Migration;

class Install extends Migration
{
    public function safeUp()
    {
        // create our table
        $this->createTable('{{%snitch_collisions}}', [
            'id' => $this->primaryKey(),
            'elementId' => $this->integer()->notNull(),
            'userId' => $this->integer()->notNull(),
            'whenEntered' => $this->dateTime()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        // foreign keys: our userId must be a user id, our elementId must be an element id
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%snitch_collisions}}', 'userId'),
            '{{%snitch_collisions}}', 'userId', '{{%users}}', 'id', 'CASCADE', null);
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%snitch_collisions}}', 'elementId'),
            '{{%snitch_collisions}}', 'elementId', '{{%elements}}', 'id', 'CASCADE', null);
    }

    public function safeDown()
    {
        // remove the table on uninstall
        $this->dropTableIfExists('{{%snitch_collisions}}');
    }
}