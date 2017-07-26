<?php

/*
 * This file is part of the 2amigos/yii2-export-grid-button-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportgrid;

use dosamigos\exportgrid\bundles\ExportGridAsset;
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
 * echo dosamigos\exportgrid\ExportGridButton::widget(
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
    /**
     * @var bool if set to true, then the plugin will not render the option "all". If that option is checked on the
     * dropdown, the client plugin will call the URL set on clientOptions with the "format" parameter and it will be
     * the action call the responsible to force download all records.
     */
    public $useClientExportOnly = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->selector === null) {
            throw new InvalidConfigException('"selector" cannot be empty');
        }
        $this->encodeLabel = false;
        $this->exportClientOptions['table'] = new JsExpression("{$this->options['id']}exportGrid");
        $this->initDropdownItems();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();

        return parent::run();
    }

    /**
     * Initializes the dropdown items
     */
    public function initDropdownItems()
    {
        $id = $this->options['id'];
        if (!$this->useClientExportOnly) {
            $this->dropdown['items'][] = [
                'label' => Html::checkbox('select_all') . '&nbsp;All records',
                'url' => '#',
                'linkOptions' => [
                    'id' => $id . 'allexportGrid',
                    'data-type' => 'all'
                ]
            ];
        }
        foreach ($this->types as $type) {
            $this->dropdown['items'][] = [
                'label' => Html::tag('span', '', ['class' => "icon-{$type}-file"]) . " {$type}",
                'url' => '#',
                'linkOptions' => [
                    'id' => $id . $type . 'exportGrid',
                    'data-type' => $type,
                ]
            ];
        }
        $this->dropdown['encodeLabels'] = false;
    }

    /**
     * Registers client script for the plugin
     */
    protected function registerClientScript()
    {
        $js = [];
        $id = $this->options['id'];
        $view = $this->getView();

        ExportGridAsset::register($view);

        $options = Json::encode($this->exportClientOptions);

        $js[] = "var {$id}exportGrid=jQuery('{$this->selector}');";
        foreach ($this->types as $type) {
            $el = $id . $type . 'exportGrid';
            $js[] = "jQuery('#$el').exportGrid($options);";
        }
        $view->registerJs(implode("\n", $js));
    }
}
