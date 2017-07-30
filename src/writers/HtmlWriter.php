<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\writers;

use Box\Spout\Writer\AbstractWriter;
use Yii;

class HtmlWriter extends AbstractWriter
{
    /**
     * @var string the path to the view files
     */
    protected $viewPath;

    /**
     * HtmlWriter constructor.
     */
    public function __construct()
    {
        $this->viewPath = dirname(__FILE__) . '/../views/html';
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function openWriter()
    {
        $header = Yii::$app->view->renderPhpFile($this->viewPath . '/_header.php');
        fwrite($this->filePointer, $header);
    }

    /**
     * @inheritdoc
     */
    protected function addRowToWriter(array $dataRow, $style)
    {
        $row = '<tr>' . implode("\n", $dataRow) . '</tr>';
        fwrite($this->filePointer, $row);
    }

    /**
     * @inheritdoc
     */
    protected function closeWriter()
    {
        $footer = Yii::$app->view->renderPhpFile($this->viewPath . '/_footer.php');
        fwrite($this->filePointer, $footer);
    }
}
