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
use dosamigos\exportable\iterators\DataProviderIterator;
use dosamigos\exportable\iterators\SourceIterator;
use dosamigos\exportable\mappers\ColumnValueMapper;
use yii\data\BaseDataProvider;
use yii\grid\GridView;

class ExportableService implements ExportableServiceInterface
{
    public function run(GridView $grid, $type, array $columns, $filename)
    {
        /** @var BaseDataProvider $dataProvider */
        $dataProvider = $grid->dataProvider;
        $mapper = new ColumnValueMapper($grid->columns, $columns);
        $source = new SourceIterator(new DataProviderIterator($dataProvider, $mapper));
        $writer = WriterFactory::create($type);

        $writer->openToBrowser($filename);

        foreach ($source as $data) {
            $writer->addRow($data);
        }
        $writer->close();
    }
}
