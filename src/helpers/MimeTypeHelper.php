<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\helpers;

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
            case TypeHelper::CSV:
                $mime = 'text/csv';
                break;
            case TypeHelper::TXT:
                $mime = 'text/plain';
                break;
            case TypeHelper::XML:
                $mime = 'text/xml';
                break;
            case TypeHelper::JSON:
                $mime = 'application/json';
                break;
            case TypeHelper::XLSX:
                $mime = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case TypeHelper::ODS:
                $mime = 'application/vnd.oasis.opendocument.spreadsheet';
                break;
        }

        return $mime;
    }
}
