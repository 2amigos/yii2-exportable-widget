<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\factory;

use Box\Spout\Common\Helper\GlobalFunctionsHelper;
use Box\Spout\Writer\WriterFactory as BaseFactory;
use dosamigos\exportable\helpers\TypeHelper;
use dosamigos\exportable\writers\HtmlWriter;
use dosamigos\exportable\writers\JsonWriter;
use dosamigos\exportable\writers\TextWriter;
use dosamigos\exportable\writers\XmlWriter;

class WriterFactory extends BaseFactory
{
    /**
     * @inheritdoc
     */
    public static function create($writerType)
    {
        $writer = null;

        switch ($writerType) {
            case TypeHelper::TXT:
                $writer = new TextWriter();
                break;
            case TypeHelper::JSON:
                $writer = new JsonWriter();
                break;
            case TypeHelper::XML:
                $writer = new XmlWriter();
                break;
            case TypeHelper::HTML:
                $writer = new HtmlWriter();
                break;
            default:
                return parent::create($writerType);
        }

        $writer->setGlobalFunctionsHelper(new GlobalFunctionsHelper());

        return $writer;
    }
}
