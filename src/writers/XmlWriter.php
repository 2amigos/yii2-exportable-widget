<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\writers;

use Box\Spout\Writer\AbstractWriter;
use dosamigos\exportable\exceptions\InvalidDataFormatException;
use RuntimeException;

class XmlWriter extends AbstractWriter
{
    /**
     * @var string Content-Type value for the header
     */
    protected static $headerContentType = 'text/xml';
    /**
     * @var string the root element of the data to be exported
     */
    protected $rootElement = 'data';
    /**
     * @var string the child element of each row data
     */
    protected $childElement = 'row';

    /**
     * @inheritdoc
     */
    protected function openWriter()
    {
        fwrite($this->filePointer, sprintf("<?xml version=\"1.0\" ?>\n<%s>\n", $this->rootElement));
    }

    /**
     * @inheritdoc
     */
    protected function addRowToWriter(array $dataRow, $style)
    {
        fwrite($this->filePointer, sprintf("<%s>\n", $this->childElement));
        foreach ($dataRow as $key => $value) {
            $this->generateNode($key, $value);
        }
        fwrite($this->filePointer, sprintf("</%s>\n", $this->childElement));
    }

    /**
     * @inheritdoc
     */
    protected function closeWriter()
    {
        fwrite($this->filePointer, sprintf("</%s>\n", $this->rootElement));
    }

    /**
     * @param string $name
     * @param string $value
     */
    protected function generateNode($name, $value)
    {
        if (is_array($value)) {
            throw new RuntimeException('Not implemented');
        } elseif (is_scalar($value) || is_null($value)) {
            fwrite($this->filePointer, sprintf("<%s><![CDATA[%s]]></%s>\n", $name, $value, $name));
        } else {
            throw new InvalidDataFormatException('Invalid data');
        }
    }
}
