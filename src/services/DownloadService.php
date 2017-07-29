<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\services;

use dosamigos\exportable\contracts\DownloadServiceInterface;
use Yii;

class DownloadService implements DownloadServiceInterface
{
    /**
     * @inheritdoc
     */
    public function run($filename, $mime, $contents)
    {
        $this->clearBuffers();

        Yii::$app->getResponse()->sendContentAsFile($contents, $filename, ['mime' => $mime]);

        Yii::$app->end();
    }

    /**
     * Clean (erase) the output buffers and turns off output buffering
     */
    protected function clearBuffers()
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }
}
