<?php

/*
 * This file is part of the 2amigos/yii2-export-grid-button-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportgrid\services;

use dosamigos\exportgrid\contracts\DownloadServiceInterface;
use Yii;

class DownloadService implements DownloadServiceInterface
{
    /**
     * @inheritdoc
     */
    public function run($filename, $mime, $contents)
    {
        $this->clearBuffers();

        Yii::$app->getResponse()->getHeaders()
            ->set('Pragma', 'public')
            ->set('Expires', '0')
            ->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->set('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->set('Content-type', $mime . '; charset=utf-8');

        echo $contents;

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
