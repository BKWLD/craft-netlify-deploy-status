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
class StatusController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/craft-netlify-deploy-status/status
     *
     * @return mixed
     */
    public function actionIndex()
    {

        Craft::$app->getView()->registerAssetBundle(ActivityAsset::class);

        $query = (new Query())
            ->select(['s.*', 'w.name'])
            ->from(['{{%craftnetlifydeploystatus_statuses}} s'])
            ->leftJoin('{{%craftnetlifydeploystatus_webhooks}} w', '[[w.id]] = [[s.webhookId]]')
            ->orderBy(['id' => SORT_DESC]);

        $paginator = new Paginator($query, [
            'currentPage' => $this->request->getPageNum(),
        ]);

        $statuses = $paginator->getPageResults();

        return $this->renderTemplate('craft-netlify-deploy-status/_activity/index', [
            'statuses' => $statuses,
            'pageInfo' => Paginate::create($paginator),
        ]);
    }

    /**
     * Clears the requests table.
     *
     * @return Response
     * @since 2.3.0
     */
    public function actionClear(): Response
    {
        Db::delete('{{%craftnetlifydeploystatus_statuses}}');
        return $this->redirect('craft-netlify-deploy-status/');
    }
}
