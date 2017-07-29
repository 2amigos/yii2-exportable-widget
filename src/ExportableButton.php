<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable;

use dosamigos\exportable\bundles\ExportGridAsset;
use dosamigos\exportable\helpers\TypeHelper;
use Yii;
use yii\bootstrap\ButtonDropdown;

class ExportableButton extends ButtonDropdown
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
     * 'types' => [ 'xml' => 'As XML']
     * ```
     *
     * Will display only XML as the unique type of exportation. Defaults to all possible options available.
     *
     * The items are a key => label structure. The types (keys) will be used as a post value that will define the type
     * that needs to be exported whether by the ExportableAction or the ExportableBehavior when used with our
     * GridView Library.
     *
     * @see https://github.com/2amigos/yii2-grid-view-library
     */
    public $types = [
        TypeHelper::CSV => '<span class="label">.csv</span> As CSV',
        TypeHelper::XLSX => '<span class="label">.xlsx</span> As Excel 2007+',
        TypeHelper::ODS => '<span class="label">.ods</span> As Open Document Spreadsheet',
        TypeHelper::JSON => '<span class="label">.json</span> As JSON',
        TypeHelper::XML => '<span class="label">.csv</span> As XML',
        TypeHelper::TXT => '<span class="label">.csv</span> As Text'
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

        foreach ($this->types as $type => $label) {
            $this->dropdown['items'][] = [
                'label' => $label,
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
