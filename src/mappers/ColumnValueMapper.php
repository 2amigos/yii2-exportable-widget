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
     * ColumnValueMapper constructor.
     *
     * @param array $columns
     * @param array $exportableColumns
     */
    public function __construct(array $columns, array $exportableColumns = [])
    {
        $this->columns = $columns;
        $this->exportableColumns = $exportableColumns;
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
                $key = $model instanceof ActiveRecordInterface
                    ? $model->getPrimaryKey()
                    : $model[$column->attribute];
                $value = $this->getColumnValue($model, $key, $index, $column);
                $header = $this->getColumnHeader($column, $model);
                $row[$header] = $value;
            }
        }

        return $row;
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
        foreach ($this->columns as $column) {
            $headers[] = $this->getColumnHeader($column, $model);
        }

        return $headers;
    }

    /**
     * Checks whether the column is exportable or not
     *
     * @param string $columnName
     *
     * @return bool
     */
    protected function isColumnExportable($columnName)
    {
        if (empty($this->exportableColumns)) {
            return true;
        }

        return in_array($columnName, $this->exportableColumns);
    }

    /**
     * Get the column generated value from the column
     *
     * @param $model
     * @param string $key
     * @param int $index
     * @param mixed $column
     *
     * @return string
     */
    protected function getColumnValue($model, $key, $index, $column)
    {
        /** @var Column $column */
        if ($column instanceof ActionColumn || $column instanceof CheckboxColumn) {
            return '';
        } elseif ($column instanceof DataColumn) {
            /** todo: think whether HTML should be used (getDataCellValue()) */
            return ArrayHelper::getValue($model, $column->attribute);
        } elseif ($column instanceof Column) {
            return $column->content !== null
                ? call_user_func($column->content, $model, $key, $index, $this)
                : $column->grid->emptyText;
        }

        return '';
    }

    /**
     * Gets columns header
     *
     * @param $column
     * @param $model
     *
     * @return string
     */
    protected function getColumnHeader($column, $model)
    {
        if (!($column instanceof DataColumn)) {
            return $column->header;
        }

        return $column->label !== null
            ? $column->label
            : (!empty($model) && $model instanceof Model
                ? $model->getAttributeLabel($column->attribute)
                : $column->attribute);
    }
}
