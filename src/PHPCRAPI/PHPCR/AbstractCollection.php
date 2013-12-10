<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\PHPCR;

use PHPCRAPI\PHPCR\Exception\CollectionUnknownKeyException;

/**
 * AbstractCollection provides collection management
 *
 * @api
 */
abstract class AbstractCollection
{
    private $items = array();

    public function __construct(array $items = array())
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Add an item to the collection
     *
     * @param Item $item The item
     *
     * @return CollectionInterface $this The used collection
     *
     * @api
     */
    public function add(CollectionItemInterface $item)
    {
        $this->items[$item->getName()] = $item;

        return $this;
    }

    /**
     * Remove an item from the collection
     *
     * @param string $name The name of the item
     *
     * @return boolean Returns true in case of success
     *
     * @throws CollectionUnknownKeyException if the item does not exist in the collection
     *
     * @api
     */
    public function remove($name)
    {
        if (!array_key_exists($name, $this->items)) {
            throw new CollectionUnknownKeyException(sprintf('Item name=%s does not exist in collection',$name));
        }

        unset($this->items[$name]);

        return true;
    }

    /**
     * Get an item from the collection
     *
     * @param string $name The name of the item
     *
     * @return Repository The found item
     *
     * @throws CollectionUnknownKeyException if the item does not exist in the collection
     *
     * @api
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->items)) {
            throw new CollectionUnknownKeyException(sprintf('Item name=%s does not exist in collection',$name));
        }

        return $this->items[$name];
    }

    /**
     * Test existence of an item from the collection
     *
     * @param string $name The name of the item
     *
     * @return boolean true or false
     *
     * @api
     */
    public function has($name)
    {
        return array_key_exists($name, $this->items);
    }

    /**
     * Get all the items stored in the collection
     *
     * @return array The items
     *
     * @api
     */
    public function getAll()
    {
        return $this->items;
    }

    /**
     * @see \IteratorAggregate::getIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->repositories);
    }
}
