<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\PHPCR;

use PHPCR\NodeInterface;

/**
 * Node is a wrapper for a PHPCR Node
 * Helps us to add metadata to it and provides more control possibilities
 *
 * @api
 */
class Node
{
    /**
     * PHPCR Node the session refers to
     *
     * @var \PHPCR\NodeInterface $session The PHPCR Session
     */
    private $node;

    /**
     * Node constructor
     *
     * @param \PHPCR\NodeInterface The wrapped node
     *
     * @api
     */
    public function __construct(NodeInterface $node)
    {
        $this->node = $node;
    }

    /**
    * @see \PHPCR\NodeInterface::getNode
    */
    public function getNode($path)
    {
        return new Node($this->node->getNode($path));
    }

    /**
    * @see \PHPCR\NodeInterface::getNodes
    */
    public function getNodes($filter = null)
    {
        $nodes = $this->node->getNodes($filter);
        foreach ($nodes as $name=>$node) {
            $nodes[$name] = new Node($node);
        }

        return $nodes;
    }

    /**
    * @see \PHPCR\NodeInterface::getParent
    */
    public function getParent()
    {
        return new Node($this->node->getParent());
    }

    /**
     * Call bridge with the wrapped PHPCR Node
     *
     * @param string $funcName Function's name
     * @param array  $args     Function's arguments
     */
    public function __call($funcName, $args)
    {
        return call_user_func_array(array($this->node, $funcName), $args);
    }

    /**
     * Return a the minimum tree to display for a node
     *
     * @return array The tree
     *
     * @api
     */
    public function getReducedTree()
    {
        $parseTree = function (Node $node, Node $target) use ( &$parseTree ) {
            if (substr($target->getPath(),0, strlen($node->getPath())) != $node->getPath()) {
                return array();
            }
            $tree = array();

            foreach ($node->getNodes() as $child) {
                $tree[] = array(
                    'name'          =>  $child->getName(),
                    'path'          =>  $child->getPath(),
                    'hasChildren'   =>  $child->hasNodes(),
                    'children'      =>  $parseTree($child, $target)
                );
            }

            return $tree;
        };

        $treeFactory = function($parent, $node) use ($parseTree) {
            return [ '/' => [
                'name'          =>  '/',
                'path'          =>  '/',
                'hasChildren'   =>  $parent->hasNodes(),
                'children'      =>  $parseTree($parent, $node)
            ]];
        };

        if ($this->getPath() == '/') {
            return $treeFactory($this, $this);
        }

        $parent = $this;
        do {
            $parent = $parent->getParent();
        } while ($parent->getPath() != '/');

        return $treeFactory($parent, $this);
    }

    /**
     * Convert node's properties to array
     *
     * @return array Properties
     *
     * @api
     */
    public function getPropertiesAsArray()
    {
        $array = array();

        foreach ($this->getProperties() as $property) {
            $array[$property->getName()] = [ 'value' => $property->getValue(), 'type' => $property->getType() ];
        }

        return $array;
    }
}
