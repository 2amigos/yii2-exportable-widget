<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\contracts;

interface DownloadServiceInterface
{
    /**
     * Runs the force download of the file
     *
     * @param string $filename
     * @param string $mime
     * @param string $contents
     */
    public function run($filename, $mime, $contents);
}
