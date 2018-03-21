<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\services;

use dosamigos\exportable\contracts\ExportableServiceInterface;
use dosamigos\exportable\factory\WriterFactory;
use dosamigos\exportable\helpers\TypeHelper;
use dosamigos\exportable\iterators\DataProviderIterator;
use dosamigos\exportable\iterators\SourceIterator;
use dosamigos\exportable\mappers\ColumnValueMapper;
use Yii;
use yii\data\BaseDataProvider;
use yii\grid\GridView;

class ExportableService implements ExportableServiceInterface
{
    /**
     * @inheritdoc
     */
    public function run(GridView $grid, $type, array $columns, $filename)
    {
        /** @var BaseDataProvider $dataProvider */
        $dataProvider = $grid->dataProvider;
        $mapper = new ColumnValueMapper($grid->columns, $columns, $type === TypeHelper::HTML);
        $source = new SourceIterator(new DataProviderIterator($dataProvider, $mapper));
        $model = $dataProvider->getTotalCount() > 0 ? $dataProvider->models[0] : null;
        $writer = WriterFactory::create($type);

        $this->clearBuffers();
        ob_start();
        $writer->openToBrowser($filename);
        if ($model !== null && !in_array($type, [TypeHelper::JSON, TypeHelper::XML])) {
            $writer->addRow($mapper->getHeaders($model));
        }
        foreach ($source as $data) {
            $writer->addRow($data);
        }
        $writer->close();

        Yii::$app->end();
    }

    /**
     * Clean (erase) the output buffers and turns off output buffering
     */
    protected function clearBuffers()
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }
}
