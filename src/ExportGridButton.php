<?php

/*
 * This file is part of the 2amigos/yii2-export-grid-button-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\gridexport;

use dosamigos\gridexport\bundles\ExportGridAsset;
use yii\base\InvalidConfigException;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * ExportGridButton renders a dropdown button with different options to export a table providing its CSS selector.
 *
 * For example,
 *
 * ```php
 * echo dosamigos\gridexport\ExportGridButton::widget(
 * [
 *  'label' => 'Export Bookings',
 *  'selector' => '#bookings-grid > table',
 *  'split' => true,
 *  'exportClientOptions' => [
 *      'ignoredColumns' => [0, 7],
 *      'useDataUri' => false,
 *      'url' => \yii\helpers\Url::to('download')
 *  ]
 * ]
 * );
 *
 * ```
 */
class ExportGridButton extends ButtonDropdown
{
    /**
     * @var string the table id or CSS selector
     */
    public $selector;
    /**
     * @var string the button label
     */
    public $label = 'Export';
    /**
     * @var array the options for the underlying GridExport JS plugin.
     * Please refer to the plugin file code for its options. These options are configured globally for all types of
     * export.
     */
    public $exportClientOptions = [];
    /**
     * @var array the export types available. You can modify the list to display only certain types. For example,
     *
     * ```
     * 'types' => [ 'xml' ]
     * ```
     *
     * Will display only XML as the unique type of exportation. Defaults to all possible options available.
     */
    public $types = [
        'xml',
        'csv',
        'pdf',
        'json',
        'html'
    ];

    public function init()
    {
        parent::init();
        if ($this->selector === null) {
            throw new InvalidConfigException('"selector" cannot be empty');
        }
        $this->encodeLabel = false;
        $this->exportClientOptions['table'] = new JsExpression("{$this->options['id']}gridExport");
        $this->initDropdownItems();
    }

    public function run()
    {
        $this->registerTableExportPlugin();

        return parent::run();
    }

    public function initDropdownItems()
    {
        $id = $this->options['id'];
        foreach ($this->types as $type) {
            $this->dropdown['items'][] = [
                'label' => Html::tag('span', '', ['class' => "icon-{$type}-file"]) . " {$type}",
                'url' => '#',
                'linkOptions' => [
                    'id' => $id . $type . 'gridExport',
                    'data-type' => $type,
                ]
            ];
        }
        $this->dropdown['encodeLabels'] = false;
    }

    protected function registerTableExportPlugin()
    {
        $js = [];
        $id = $this->options['id'];
        $view = $this->getView();

        ExportGridAsset::register($view);

        $options = Json::encode($this->exportClientOptions);

        $js[] = "var {$id}gridExport=jQuery('{$this->selector}');";
        foreach ($this->types as $type) {
            $el = $id . $type . 'gridExport';
            $js[] = "jQuery('#$el').gridExport($options);";
        }
        $view->registerJs(implode("\n", $js));
    }
}
