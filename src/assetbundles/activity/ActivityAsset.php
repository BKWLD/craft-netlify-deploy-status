<?php
/**
 * Craft Netlify Deploy Status plugin for Craft CMS 3.x
 *
 * Craft plugin that shows Netlify deploy statuses
 *
 * @link      https://bukwild.com
 * @copyright Copyright (c) 2021 Isaaz Garcia
 */

namespace bukwild\craftnetlifydeploystatus\assetbundles\activity;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * Activity index asset bundle
 *
 * @author    Isaaz Garcia
 * @package   CraftNetlifyDeployStatus
 * @since     1.0.0
 */
class ActivityAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';

    public $depends = [
        CpAsset::class,
    ];

    public $css = [
        'css/activity.css',
    ];

    public $js = [
        'js/Activity.js',
    ];
}
