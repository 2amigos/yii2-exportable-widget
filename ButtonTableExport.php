<?php
/**
 * @copyright Copyright (c) 2014 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\tableexport;

use yii\base\InvalidConfigException;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * ButtonTableExport renders a dropdown button with different options to export a table providing its CSS selector.
 *
 * For example,
 *
 * ```php
 * echo dosamigos\tableexport\ButtonTableExport::widget(
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
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\tableexport
 */
class ButtonTableExport extends ButtonDropdown
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
     * @var array the options for the underlying TableExport JS plugin.
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
        $this->exportClientOptions['table'] = new JsExpression("{$this->options['id']}tableExport");
        $this->initDropdownItems();

    }

    public function run()
    {
        parent::run();
        $this->registerTableExportPlugin();
    }

    public function initDropdownItems()
    {
        $id = $this->options['id'];
        foreach ($this->types as $type) {
            $this->dropdown['items'][] = [
                'label' => Html::tag('span', '', ['class' => "icon-{$type}-file"]) . " {$type}",
                'linkOptions' => [
                    'id' => $id . $type . 'tableExport',
                    'data-type' => $type
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

        ButtonTableExportAsset::register($view);

        $options = Json::encode($this->exportClientOptions);

        $js[] = "var {$id}tableExport=jQuery('{$this->selector}');";
        foreach ($this->types as $type) {
            $el = $id . $type . 'tableExport';
            $js[] = "jQuery('#$el').tableExport($options);";
        }
        $view->registerJs(implode("\n", $js));
    }
}