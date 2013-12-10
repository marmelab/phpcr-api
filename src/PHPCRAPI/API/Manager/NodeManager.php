<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\API\Manager;

use PHPCR\ItemNotFoundException;
use PHPCR\AccessDeniedException as PHPCRAccessDeniedException;
use PHPCR\RepositoryException;
use PHPCRAPI\API\Exception\ResourceNotFoundException;
use PHPCRAPI\API\Exception\AccessDeniedException;
use PHPCRAPI\API\Exception\InternalServerErrorException;
use PHPCRAPI\PHPCR\Node;

/**
 * Available function for node management by the API
 *
 * @api
 */
class NodeManager
{
    private $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function getReducedTree($path)
    {
        try {
            return Node::getReducedTree($this->node);
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function getName()
    {
        try {
            return $this->node->getName();
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function getParent()
    {
        try {
            return new NodeManager($this->node->getParent());
        } catch (ItemNotFoundException $e) {
            throw new ResourceNotFoundException('This node is the root node of a workspace');
        } catch (PHPCRAccessDeniedException $e) {
            throw new AccessDeniedException('Current session does not have sufficient access to retrieve the parent of this node')
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function getPath()
    {
        try {
            return $this->node->getPath();
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function getPropertiesToArray()
    {
        try {
            return $this->node->getPropertiesToArray();
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function getChildren($filter = null)
    {
        try {
            $children = array();

            foreach ($this->node->getNodes($filter) as $child) {
                $children[] = new NodeManager($child);
            }

            return $children;
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }
}
