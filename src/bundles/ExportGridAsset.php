<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\bundles;

use yii\web\AssetBundle;

class ExportGridAsset extends AssetBundle
{
    public $sourcePath = '@vendor/2amigos/yii2-exportable-widget/assets';

    public $js = [
        'js/dosamigos-export-grid.button.js'
    ];

    public $css = [
        'css/hawcons.css'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
