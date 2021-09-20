<?php
/**
 * Craft Netlify Deploy Status plugin for Craft CMS 3.x
 *
 * Craft plugin that shows Netlify deploy statuses
 *
 * @link      https://bukwild.com
 * @copyright Copyright (c) 2021 Isaaz Garcia
 */

namespace bukwild\craftnetlifydeploystatus\controllers;

use bukwild\craftnetlifydeploystatus\assetbundles\activity\ActivityAsset;

use Craft;
use craft\web\Controller;
use craft\db\Paginator;
use craft\db\Query;
use craft\helpers\Db;
use craft\helpers\Json;
use craft\web\twig\variables\Paginate;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\base\InvalidArgumentException;

/**
 * Status Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Isaaz Garcia
 * @package   CraftNetlifyDeployStatus
 * @since     1.0.0
 */
class NotificationController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['create'];

    // Public Methods
    // =========================================================================
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config = []);
        $this->enableCsrfValidation = false;
    }

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/craft-netlify-deploy-status/status/notification
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $this->requirePostRequest();
        $uid = $this->request->getRequiredQueryParam('id');

        // Check webhook Id
        if (!$uid){
            return false;
        }

        $result = (new Query())
            ->select(['id'])
            ->from(['{{%craftnetlifydeploystatus_webhooks}}'])
            ->where(['uid' => $uid])
            ->one();

        if ($result === null) {
            throw new InvalidArgumentException('Invalid webhook ID: ' . $uid);
        }

        // Create Status
        $bodyParams = $this->request->getBodyParams();
        $name = $bodyParams['name'];
        $state = $bodyParams['state'];
        $url = $bodyParams['url'];
        $adminUrl = $bodyParams['admin_url'] .'/deploys/' .$bodyParams['id'];
        $deployUrl = $bodyParams['deploy_url'];
        $commitUrl = $bodyParams['commit_url'];

        $trigger = '';
        $searchString = "Deploy triggered by hook: ";
        if ($bodyParams['title'] !== null && strpos($bodyParams['title'], $searchString) !== false){
            $trigger = explode($searchString, $bodyParams['title'])[1];
        }

        Db::insert('{{%craftnetlifydeploystatus_statuses}}', [
            'webhookId' => $result['id'],
            'name' => $name,
            'state' => $state,
            'url' => $url,
            'adminUrl' => $adminUrl,
            'deployUrl' => $deployUrl,
            'commitUrl' => $commitUrl,
            'trigger' => $trigger,
        ]);

        Craft::$app->getSession()->setNotice(Craft::t('craft-netlify-deploy-status', 'Netlify Status created.'));

        return true;
    }
}
