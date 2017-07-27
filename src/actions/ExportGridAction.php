<?php

/*
 * This file is part of the 2amigos/yii2-export-grid-button-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportgrid\actions;

use dosamigos\exportgrid\contracts\DownloadServiceInterface;
use dosamigos\exportgrid\helpers\MimeTypeHelper;
use dosamigos\exportgrid\services\DownloadService;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;

/**
 * ExportGridAction provides the functionality to handle form requests for file download to the GridExport javascript
 * plugin.
 */
class ExportGridAction extends Action
{
    /**
     * @var string is the filename of the file to be downloaded. Defaults to "export-grid"
     */
    public $fileName = 'export-grid';
    /**
     * @var \Closure an anonymous function that is called once BEFORE forcing download. The return result of the
     * function will be rendered on the file. It should have the following signature:
     *
     * ```php
     * function ($type)
     * ```
     *
     * - `type`: Is the format type to download. The anonymous function should return the content on the selected
     * format.
     */
    public $contentValue;
    /**
     * @var DownloadServiceInterface
     */
    public $downloadService;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!is_callable($this->contentValue)) {
            throw new InvalidConfigException('"contentValue" must be a valid callable.');
        }
        if (null === $this->downloadService) {
            $this->downloadService = new DownloadService();
        }
        parent::init();
    }

    /**
     * @throws BadRequestHttpException
     * @return array|mixed
     */
    public function run()
    {
        if (Yii::$app->request->isPost) {
            $type = Yii::$app->request->post('type');
            $contents = call_user_func($this->contentValue, $type);
            $mime = MimeTypeHelper::getMimeType($type);
            $filename = $this->fileName . '.' . $type;

            if ($mime !== false) {
                $this->downloadService->run($filename, $mime, $contents);
            }
        }
        throw new BadRequestHttpException('Your request is invalid.');
    }
}
