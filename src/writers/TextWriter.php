<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\writers;

use Box\Spout\Writer\CSV\Writer;

class TextWriter extends Writer
{
    /**
     * @var string Content-Type value for the header
     */
    protected static $headerContentType = 'text/plain; charset=UTF-8';
    /**
     * @var string Defines the character used to delimit fields (one character only)
     */
    protected $fieldDelimiter = "\t";
}
