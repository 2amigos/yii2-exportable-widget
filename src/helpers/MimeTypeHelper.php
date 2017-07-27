<?php

/*
 * This file is part of the 2amigos/yii2-export-grid-button-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportgrid\helpers;

/**
 *
 * MimeType.php
 *
 * Date: 27/7/17
 * Time: 10:12
 * @author Antonio Ramirez <hola@2amigos.us>
 */
class MimeTypeHelper
{
    /**
     * Returns the mime type for the
     *
     * @param $type
     *
     * @return bool|string
     */
    public static function getMimeType($type)
    {
        $mime = false;

        switch ($type) {
            case 'csv':
                $mime = 'text/csv';
                break;
            case 'html':
                $mime = 'text/html';
                break;
            case 'excel':
                $mime = 'application/vnd.ms-excel';
                break;
            case 'xml':
                $mime = 'text/xml';
                break;
            case 'json':
                $mime = 'application/json';
                break;
        }

        return $mime;
    }
}
