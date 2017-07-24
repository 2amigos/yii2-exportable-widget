<?php

/*
 * This file is part of the 2amigos/yii2-export-grid-button-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\gridexport\bundles;

use yii\web\AssetBundle;

class ExportGridAsset extends AssetBundle
{
    public $sourcePath = '@vendor/2amigos/yii2-export-grid-button-widget/assets';

    public $js = [
        'js/dosamigos-export-grid.button.js'
    ];

    public $css = [
        'css/dosamigos-export-grid.font.css'
    ];

    public $depends = [
        'dosamigos\assets\DosAmigosAsset',
        'dosamigos\gridexport\bundles\JspdfAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
