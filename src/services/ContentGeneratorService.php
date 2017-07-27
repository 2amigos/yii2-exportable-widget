<?php

/*
 * This file is part of the 2amigos/yii2-export-grid-button-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportgrid\services;

use dosamigos\exportgrid\contracts\ContentGeneratorServiceInterface;
use dosamigos\exportgrid\exceptions\UnknownExportTypeException;
use dosamigos\exportgrid\iterators\DataProviderIterator;
use dosamigos\exportgrid\mappers\ColumnValueMapper;
use Exporter\Handler;
use Exporter\Source\IteratorSourceIterator;
use Exporter\Writer\CsvWriter;
use Exporter\Writer\JsonWriter;
use Exporter\Writer\XlsWriter;
use Exporter\Writer\XmlWriter;
use Yii;
use yii\data\BaseDataProvider;
use yii\grid\GridView;

class ContentGeneratorService implements ContentGeneratorServiceInterface
{
    /**
     * @inheritdoc
     */
    public function run(GridView $grid, $type, array $columns)
    {
        /** @var BaseDataProvider $dataProvider */
        $dataProvider = $grid->dataProvider;
        $models = $dataProvider->getModels();
        $mapper = new ColumnValueMapper($grid->columns, $columns);
        $source = new IteratorSourceIterator(new DataProviderIterator($dataProvider, $mapper));
        $filename = Yii::getAlias('@runtime') . '/' . md5(time()) . '.' . $type;
        $writer = $this->getWriter($type, $filename);

        Handler::create($source, $writer)->export();

        return file_get_contents($filename);
    }

    /**
     * Returns the writer to generate the contents
     *
     * @param string $type
     * @param string $filename
     *
     * @throws UnknownExportTypeException
     * @return CsvWriter|JsonWriter|XlsWriter|XmlWriter
     */
    protected function getWriter($type, $filename)
    {
        switch ($type) {
            case 'csv':
                return new CsvWriter($filename);
            case 'xls':
                return new XlsWriter($filename);
            case 'xml':
                return new XmlWriter($filename);
            case 'json':
                return new JsonWriter($filename);
            default:
                throw new UnknownExportTypeException();
        }
    }
}
