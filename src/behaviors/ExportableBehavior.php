<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\behaviors;

use dosamigos\exportable\contracts\ExportableServiceInterface;
use dosamigos\exportable\exceptions\UnknownExportTypeException;
use dosamigos\exportable\helpers\TypeHelper;
use dosamigos\exportable\services\ExportableService;
use dosamigos\grid\contracts\RunnableBehaviorInterface;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\grid\GridView;

class ExportableBehavior extends Behavior implements RunnableBehaviorInterface
{
    /**
     * @var string the filename that will be used for the download. It must be without file extension!
     */
    public $filename = 'exportable';
    /**
     * @var ExportableServiceInterface
     */
    public $exportableService;
    /**
     * @var array the columns to export
     */
    public $columns = [];
    /**
     * @var array of configurable writers by type. The type is the key.
     */
    public $writers = [
        TypeHelper::CSV => 'Box\Spout\Writer\CSV\Writer',
        TypeHelper::XLSX => 'Box\Spout\Writer\XLSX\Writer',
        TypeHelper::ODS => 'Box\Spout\Writer\ODS\Writer',
        TypeHelper::XML => 'dosamigos\exportable\XmlWriter',
        TypeHelper::JSON => 'dosamigos\exportable\JsonWriter',
        TypeHelper::TXT => 'dosamigos\exportable\TextWriter',
        TypeHelper::HTML => 'dosamigos\exportable\HtmlWriter'
    ];
    /**
     * @var string
     */
    protected $type;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ((int)Yii::$app->request->post('export') === 1) {
            $this->type = Yii::$app->request->post('type', TypeHelper::XLSX);
            if (!array_key_exists($this->type, $this->writers)) {
                throw new UnknownExportTypeException(
                    sprintf(
                        'Unknown type "%s". Make sure writers are properly configured.',
                        $this->type
                    )
                );
            }
            if (null === $this->exportableService) {
                $this->exportableService = new ExportableService();
            }
            if (!$this->exportableService instanceof ExportableServiceInterface) {
                throw new InvalidConfigException(
                    sprintf(
                        'The "exportableService" class must implement %s',
                        ExportableServiceInterface::class
                    )
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->type) {
            /** @var GridView $owner */
            $owner = $this->owner;
            $filename = $this->filename . '.' . $this->type;
            $this->exportableService->run($owner, $this->type, $this->columns, $filename);
        }
    }
}
