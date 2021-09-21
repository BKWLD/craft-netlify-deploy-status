<?php
/**
 * Craft Netlify Deploy Status plugin for Craft CMS 3.x
 *
 * Craft plugin that shows Netlify deploy statuses
 *
 * @link      https://bukwild.com
 * @copyright Copyright (c) 2021 Bukwild
 */

namespace bukwild\craftnetlifydeploystatus\models;

use bukwild\craftnetlifydeploystatus\CraftNetlifyDeployStatus;

use Craft;
use craft\base\Model;

/**
 * Webhook Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Bukwild
 * @package   CraftNetlifyDeployStatus
 * @since     1.0.0
 */
class Webhook extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        return [
            [['name'], 'trim'],
            [['name'], 'required'],
        ];
    }
}
