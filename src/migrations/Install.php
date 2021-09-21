<?php
/**
 * Craft Netlify Deploy Status plugin for Craft CMS 3.x
 *
 * Craft plugin that shows Netlify deploy statuses
 *
 * @link      https://bukwild.com
 * @copyright Copyright (c) 2021 Bukwild
 */

namespace bukwild\craftnetlifydeploystatus\migrations;

use bukwild\craftnetlifydeploystatus\CraftNetlifyDeployStatus;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * Craft Netlify Deploy Status Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Bukwild
 * @package   CraftNetlifyDeployStatus
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        // craftnetlifydeploystatus_statuses table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%craftnetlifydeploystatus_statuses}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%craftnetlifydeploystatus_statuses}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    // Custom columns in the table
                    'webhookId' => $this->integer(),
                    'state' => $this->string()->notNull(),
                    'name' => $this->string()->notNull(),
                    'url' => $this->string()->notNull(),
                    'adminUrl' => $this->string(255)->notNull()->defaultValue(''),
                    'deployUrl' => $this->string(255)->null()->defaultValue(''),
                    'commitUrl' => $this->string(255)->null()->defaultValue(''),
                    'trigger' => $this->string(255)->null()->defaultValue(''),
                ]
            );
        }

        // craftnetlifydeploystatus_webhooks table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%craftnetlifydeploystatus_webhooks}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%craftnetlifydeploystatus_webhooks}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->null(),
                    'uid' => $this->uid(),
                    // Custom columns in the table
                    'name' => $this->string(255)->notNull()->defaultValue(''),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%craftnetlifydeploystatus_statuses}}', 'siteId'),
            '{{%craftnetlifydeploystatus_statuses}}',
            'webhookId',
            '{{%craftnetlifydeploystatus_webhooks}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
        // craftnetlifydeploystatus_status table
        $this->dropTableIfExists('{{%craftnetlifydeploystatus_statuses}}');

        // craftnetlifydeploystatus_webhook table
        $this->dropTableIfExists('{{%craftnetlifydeploystatus_webhooks}}');
    }
}
