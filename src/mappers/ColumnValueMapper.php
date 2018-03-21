<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\mappers;

use yii\base\Model;
use yii\db\ActiveRecordInterface;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\Column;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;

class ColumnValueMapper
{
    /**
     * @var array column definitions from GridView
     */
    protected $columns = [];
    /**
     * @var array the exportable column names
     */
    protected $exportableColumns = [];
    /**
     * @var bool whether we render HTML or not
     */
    protected $isHtml;

    /**
     * ColumnValueMapper constructor.
     *
     * @param array $columns
     * @param array $exportableColumns
     * @param bool $isHtml whether we need to render HTML or not
     */
    public function __construct(array $columns, array $exportableColumns = [], $isHtml = false)
    {
        $this->columns = $columns;
        $this->exportableColumns = $exportableColumns;
        $this->isHtml = $isHtml;
    }

    /**
     * Fetch data from the data provider and create the rows array
     *
     * @param mixed $model
     * @param $index
     *
     * @return array
     */
    public function map($model, $index)
    {
        $row = [];
        foreach ($this->columns as $column) {
            if ($this->isColumnExportable($column)) {
                /** @var DataColumn $column */
                $key = $model instanceof ActiveRecordInterface
                    ? $model->getPrimaryKey()
                    : $model[$column->attribute];

                $value = $this->getColumnValue($column, $model, $key, $index);

                $header = $this->getColumnHeader($column);
                $row[$header] = $value;
            }
        }

        return $row;
    }

    protected function getColumnValue($column, $model, $key, $index)
    {
        $value = $column->renderDataCell($model, $key, $index);
        if (!$this->isHtml) {
            $value = strip_tags($value);
        }

        return $value;
    }

    /**
     * Returns column headers
     *
     * @param $model
     *
     * @return array
     */
    public function getHeaders($model)
    {
        $headers = [];
        /** @var Column $column */
        foreach ($this->columns as $column) {
            if ($this->isColumnExportable($column)) {
                $headers[] = $this->getColumnHeader($column);
            }
        }

        return $headers;
    }

    /**
     * Checks whether the column is exportable or not
     *
     * @param Column $column
     *
     * @return bool
     */
    protected function isColumnExportable($column)
    {
        if ($column instanceof ActionColumn || $column instanceof CheckboxColumn) {
            return false;
        }

        if (empty($this->exportableColumns)) {
            return true;
        }

        return in_array($column->attribute, $this->exportableColumns);
    }

    /**
     * Gets columns header
     *
     * @param $column
     * @param $model
     *
     * @return string
     */
    protected function getColumnHeader($column)
    {
        $header = $column->renderHeaderCell();
        if (!$this->isHtml) {
            $header = strip_tags($header);
        }

        return $header;
    }
}
