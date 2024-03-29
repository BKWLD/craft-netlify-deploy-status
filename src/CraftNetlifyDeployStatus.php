<?php
/**
 * Craft Netlify Deploy Status plugin for Craft CMS 3.x
 *
 * Craft plugin that shows Netlify deploy statuses
 *
 * @link      https://bukwild.com
 * @copyright Copyright (c) 2021 Bukwild
 */

namespace bukwild\craftnetlifydeploystatus;

use bukwild\craftnetlifydeploystatus\widgets\CraftNetlifyDeployStatusWidget as CraftNetlifyDeployStatusWidgetWidget;
use bukwild\craftnetlifydeploystatus\assetbundles\app\AppAsset;
use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\events\TemplateEvent;
use craft\web\UrlManager;
use craft\web\View;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Bukwild
 * @package   CraftNetlifyDeployStatus
 * @since     1.0.0
 *
 */
class CraftNetlifyDeployStatus extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * CraftNetlifyDeployStatus::$plugin
     *
     * @var CraftNetlifyDeployStatus
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public bool $hasCpSettings = false;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public bool $hasCpSection = true;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * CraftNetlifyDeployStatus::$plugin
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

        // Checking if plugin is installed
        if (!$this->isInstalled) {
            return;
        }

        // Checking if user is guest
        $userIsGuest = Craft::$app->user->isGuest;
        if ($userIsGuest) {
            return;
        }

        self::$plugin = $this;

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['craft-netlify-deploy-status'] = 'craft-netlify-deploy-status/status/index';
                $event->rules['craft-netlify-deploy-status/manage-webhooks'] = 'craft-netlify-deploy-status/webhook/index';
            }
        );

        // Load Nav
        Event::on(
            View::class,
            View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE,
            function (TemplateEvent $event) {
                Craft::$app->getView()->registerAssetBundle(AppAsset::class);
            }
        );

/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'craft-netlify-deploy-status',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem(): ?array
    {
        $item = parent::getCpNavItem();
        $item['subnav'] = [
            'activity' => ['label' => Craft::t('webhooks', 'Activity'), 'url' => 'craft-netlify-deploy-status'],
            'manage' => ['label' => Craft::t('webhooks', 'Incoming Webhooks'), 'url' => 'craft-netlify-deploy-status/manage-webhooks'],
        ];
        return $item;
    }

    // Protected Methods
    // =========================================================================

    private function isDeploying(){
//        $result = (new Query())
//            ->select(['id'])
//            ->from(['{{%craftnetlifydeploystatus_statuses}}'])
//            ->one();

        return true;
    }
}
