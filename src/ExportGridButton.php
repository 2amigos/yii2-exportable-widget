<?php

/*
 * This file is part of the 2amigos/yii2-export-grid-button-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportgrid;

use dosamigos\exportgrid\bundles\ExportGridAsset;
use Yii;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;

class ExportGridButton extends ButtonDropdown
{
    /**
     * @var string the action to submit to download exported data
     */
    public $url;
    /**
     * @var string the button label
     */
    public $label = 'Export';
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
        //'pdf', /** todo: to be implemented */
        'json',
        'html'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->encodeLabel = false;
        $this->initDropdownItems();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();

        if (empty($this->url)) {
            $this->url = Yii::$app->request->getUrl();
        }

        return parent::run();
    }

    /**
     * Initializes the dropdown items
     */
    public function initDropdownItems()
    {
        $id = $this->options['id'];

        foreach ($this->types as $type) {
            $this->dropdown['items'][] = [
                'label' => Html::tag('span', '', ['class' => "icon-{$type}-file"]) . " {$type}",
                'url' => '#',
                'linkOptions' => [
                    'id' => $id . $type . 'exportGrid',
                    'data-type' => $type,
                    'class' => 'btn-da-export-grid'
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
        $id = $this->options['id'];
        $hash = hash('crc32', $id);
        $url = $this->url;
        $view = $this->getView();

        ExportGridAsset::register($view);

        $js = "dosamigos.exportGrid.registerHandler('.btn-da-export-grid', '{$url}', '{$hash}');";

        $view->registerJs($js);
    }
}
