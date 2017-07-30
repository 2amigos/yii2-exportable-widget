<?php

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

    protected function closeWriter()
    {
        $footer = Yii::$app->view->renderPhpFile($this->viewPath . '/_footer.php');
        fwrite($this->filePointer, $footer);
    }

}
