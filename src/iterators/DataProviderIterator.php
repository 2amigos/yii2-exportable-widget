<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\iterators;

use Countable;
use dosamigos\exportable\mappers\ColumnValueMapper;
use Iterator;
use OutOfBoundsException;
use yii\data\BaseDataProvider;
use yii\data\Pagination;

class DataProviderIterator implements Iterator, Countable
{
    /**
     * @var ColumnValueMapper|null
     */
    protected $itemMapper;
    /**
     * @var \yii\data\BaseDataProvider
     */
    private $dataProvider;
    /**
     * @var int
     */
    private $currentIndex = -1;
    /**
     * @var int
     */
    private $currentPage = 0;
    /**
     * @var int
     */
    private $totalItemCount = -1;
    /**
     * @var
     */
    private $items;

    /**
     * Constructor.
     *
     * @param BaseDataProvider $dataProvider the data provider to iterate over
     * @param ColumnValueMapper|null $itemMapper apply column transformations to Models
     * @param integer $pageSize pageSize to use for iteration. This is the number of objects loaded into memory at the same time.
     */
    public function __construct(BaseDataProvider $dataProvider, ColumnValueMapper $itemMapper = null, $pageSize = null)
    {
        $this->dataProvider = $dataProvider;
        $this->totalItemCount = $dataProvider->getTotalCount();
        $this->itemMapper = $itemMapper;
        if (($pagination = $this->dataProvider->getPagination()) === false) {
            $this->dataProvider->setPagination($pagination = new Pagination());
        }
        if ($pageSize !== null) {
            $pagination->pageSize = $pageSize;
        }
    }

    /**
     * Returns the data provider to iterate over
     * @return BaseDataProvider the data provider to iterate over
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * Gets the total number of items to iterate over
     * @return integer the total number of items to iterate over
     */
    public function getTotalItemCount()
    {
        return $this->totalItemCount;
    }

    /**
     * Gets the current item in the list.
     * This method is required by the Iterator interface.
     * @return mixed the current item in the list
     */
    public function current()
    {
        return $this->getItem($this->getCurrentIndex());
    }

    /**
     * @return int
     */
    public function getCurrentIndex()
    {
        return $this->currentIndex;
    }

    /**
     * Gets the key of the current item.
     * This method is required by the Iterator interface.
     * @return integer the key of the current item
     */
    public function key()
    {
        $pageSize = $this->getDataProvider()->getPagination()->pageSize;

        return $this->getCurrentPage() * $pageSize + $this->getCurrentIndex();
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Moves the pointer to the next item in the list.
     * This method is required by the Iterator interface.
     */
    public function next()
    {
        $pageSize = $this->getDataProvider()->getPagination()->pageSize;
        $this->currentIndex++;
        if ($this->currentIndex >= $pageSize) {
            $this->currentPage++;
            $this->currentIndex = 0;
            $this->loadPage();
        }
    }

    /**
     * Rewinds the iterator to the start of the list.
     * This method is required by the Iterator interface.
     */
    public function rewind()
    {
        $this->currentIndex = 0;
        $this->currentPage = 0;
        $this->loadPage();
    }

    /**
     * Checks if the current position is valid or not.
     * This method is required by the Iterator interface.
     * @return boolean true if this index is valid
     */
    public function valid()
    {
        return $this->key() < $this->getTotalItemCount();
    }

    /**
     * Gets the total number of items in the dataProvider.
     * This method is required by the Countable interface.
     * @return integer the total number of items
     */
    public function count()
    {
        return $this->getTotalItemCount();
    }

    /**
     * Loads a page of items
     * @return array the items from the next page of results
     */
    protected function loadPage()
    {
        $this->getDataProvider()->getPagination()->setPage($this->getCurrentPage());
        $this->getDataProvider()->prepare(true);

        return $this->items = $this->getDataProvider()->getModels();
    }

    /**
     * @param $index
     *
     * @return array
     */
    protected function getItem($index)
    {
        if (!isset($this->items[$index])) {
            throw new OutOfBoundsException('Index is not allowed be limits of current page');
        }
        if (!empty($this->itemMapper)) {
            return $this->itemMapper->map($this->items[$index], $index);
        }

        return $this->items[$index];
    }
}
