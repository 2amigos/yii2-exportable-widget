<?php

/*
 * This file is part of the 2amigos/yii2-exportable-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\exportable\iterators;

use dosamigos\exportable\contracts\SourceIteratorInterface;
use Iterator;

/**
 * SourceIterator implementation based on Iterator.
 */
class SourceIterator implements SourceIteratorInterface
{
    /**
     * @var Iterator
     */
    protected $iterator;

    /**
     * @param Iterator $iterator Iterator with string array elements
     */
    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * @return Iterator
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();
    }
}
