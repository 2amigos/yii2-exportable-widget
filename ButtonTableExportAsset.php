<?php
/**
 * @copyright Copyright (c) 2014 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\tableexport;


use yii\web\AssetBundle;

/**
 * ButtonTableExportAsset
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\tableexport
 */
class ButtonTableExportAsset extends AssetBundle
{
    public $sourcePath = '@vendor/2amigos/yii2-table-export-widget/assets';

    public $js = [
        'js/dosamigos-table-export.js',
        'js/jspdf.min.js',
    ];

    public $css = [
        'css/table-export-font.css'
    ];

    public $depends = [
        'dosamigos\assets\DosAmigosAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}