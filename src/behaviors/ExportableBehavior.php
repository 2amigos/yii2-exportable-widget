<?php

/*
 * This file is part of the 2amigos/yii2-export-grid-button-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportgrid\behaviors;

use dosamigos\exportgrid\contracts\ContentGeneratorServiceInterface;
use dosamigos\exportgrid\contracts\DownloadServiceInterface;
use dosamigos\exportgrid\helpers\MimeTypeHelper;
use dosamigos\exportgrid\services\ContentGeneratorService;
use dosamigos\exportgrid\services\DownloadService;
use dosamigos\grid\contracts\RunnableBehaviorInterface;
use Yii;
use yii\base\Behavior;
use yii\grid\GridView;

class ExportableBehavior extends Behavior implements RunnableBehaviorInterface
{
    /**
     * @var string the filename that will be used for the download. It must be without file extension!
     */
    public $filename = 'exportable';
    /**
     * @var DownloadServiceInterface
     */
    public $downloadService;
    /**
     * @var ContentGeneratorServiceInterface
     */
    public $contentGeneratorService;
    /**
     * @var array the columns to export
     */
    public $columns = [];
    /**
     * @var string
     */
    protected $type;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->type = Yii::$app->request->post('type', 'excel');

        if (null === $this->downloadService) {
            $this->downloadService = new DownloadService();
        }
        if (null === $this->contentGeneratorService) {
            $this->contentGeneratorService = new ContentGeneratorService();
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->request->post('export')) {
            /** @var GridView $owner */
            $owner = $this->owner;
            $mime = MimeTypeHelper::getMimeType($this->type);
            $filename = $this->filename . '.' . $this->type;
            $contents = $this->contentGeneratorService->run($owner, $this->type, $this->columns);
            $this->downloadService->run($filename, $mime, $contents);
        }
    }
}
