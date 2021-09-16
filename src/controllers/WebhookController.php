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

use bukwild\craftnetlifydeploystatus\assetbundles\indexcpsection\IndexCPSectionAsset;
use bukwild\craftnetlifydeploystatus\CraftNetlifyDeployStatus;

use bukwild\craftnetlifydeploystatus\models\Webhook;
use Craft;
use craft\web\Controller;
use craft\db\Paginator;
use craft\db\Query;
use craft\helpers\Db;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use craft\web\twig\variables\Paginate;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Webhook Controller
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
class WebhookController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = false;

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/craft-netlify-deploy-status/webhook
     *
     * @return mixed
     */
    public function actionIndex()
    {
        Craft::$app->getView()->registerAssetBundle(IndexCPSectionAsset::class);

        $results = (new Query())
            ->select(['*'])
            ->from(['{{%craftnetlifydeploystatus_webhooks}}'])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->renderTemplate('craft-netlify-deploy-status/_manage/index', [
            'webhooks' => $results,
        ]);

    }

    /**
     * Saves a webhook
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $id = $this->request->getBodyParam('id');
        $name = $this->request->getRequiredBodyParam('name');
        $webhook = new Webhook(compact('id', 'name'));

        $db = Craft::$app->getDb();

        if ($db->getIsMysql()) {
            $name = StringHelper::encodeMb4($webhook->name);
        } else {
            $name = $webhook->name;
        }

        if ($webhook->id) {
            Db::update('{{%craftnetlifydeploystatus_webhooks}}', [
                'name' => $name,
            ], [
                'id' => $webhook->id,
            ]);
        } else {
            Db::insert('{{%craftnetlifydeploystatus_webhooks}}', [
                'name' => $name,
            ]);

            $webhook->id = (int)$db->getLastInsertID('{{%craftnetlifydeploystatus_webhooks}}');
        }

        if (!$id) {
            Craft::$app->getSession()->setNotice(Craft::t('craft-netlify-deploy-status', 'Webhook created.'));
        }

        return $this->asJson([
            'success' => true,
            'webhook' => $webhook,
        ]);
    }

    /**
     * Deletes a webhook.
     *
     * @return Response
     */
    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $id = $this->request->getRequiredBodyParam('id');
        Db::delete('{{%craftnetlifydeploystatus_webhooks}}', ['id' => $id]);

        return $this->redirectToPostedUrl();
    }

}
