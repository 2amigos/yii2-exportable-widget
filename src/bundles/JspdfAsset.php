<?php

namespace dosamigos\gridexport\bundles;

use yii\web\AssetBundle;

class JspdfAsset extends AssetBundle
{
    public $sourcePath = '@bower/jspdf/dist';

    public $js = [
        'jspdf.min.js'
    ];
}
