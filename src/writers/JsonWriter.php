<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\writers;

use Box\Spout\Writer\AbstractWriter;

class JsonWriter extends AbstractWriter
{
    /**
     * @var int current position
     */
    protected $position;
    /**
     * @var string Content-Type value for the header
     */
    protected static $headerContentType = 'application/json';

    /**
     * @inheritdoc
     */
    protected function openWriter()
    {
        fwrite($this->filePointer, '[');
    }

    /**
     * @inheritdoc
     */
    protected function addRowToWriter(array $dataRow, $style)
    {
        fwrite($this->filePointer, ($this->position > 0 ? ',' : '') . json_encode($dataRow));

        ++$this->position;
    }

    /**
     * @inheritdoc
     */
    protected function closeWriter()
    {
        fwrite($this->filePointer, ']');
    }
}
