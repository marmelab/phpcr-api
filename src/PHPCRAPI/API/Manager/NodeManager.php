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
use PHPCR\ItemExistsException;
use PHPCR\RepositoryException;
use PHPCR\UnsupportedRepositoryOperationException;
use PHPCR\Version\VersionException;
use PHPCR\Lock\LockException;
use PHPCR\NodeType\ConstraintViolationException;
use PHPCR\PathNotFoundException;
use PHPCR\ValueFormatException;
use PHPCRAPI\API\Exception\ResourceNotFoundException;
use PHPCRAPI\API\Exception\ResourceLockedException;
use PHPCRAPI\API\Exception\ResourceConstraintViolationException;
use PHPCRAPI\API\Exception\AccessDeniedException;
use PHPCRAPI\API\Exception\InternalServerErrorException;
use PHPCRAPI\API\Exception\NotSupportedOperationException;
use PHPCRAPI\PHPCR\Node;

/**
 * Available function for node management by the API
 *
 * @api
 */
class NodeManager
{
    private $node;

    private $sessionManager;

    public function __construct(Node $node, SessionManager $sessionManager)
    {
        $this->node = $node;
        $this->sessionManager = $sessionManager;
    }

    public function getReducedTree()
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
            return new NodeManager($this->node->getParent(), $this->sessionManager);
        } catch (ItemNotFoundException $e) {
            throw new ResourceNotFoundException('This node is the root node of a workspace');
        } catch (PHPCRAccessDeniedException $e) {
            throw new AccessDeniedException('Current session does not have sufficient access to retrieve the parent of this node');
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
                $children[] = new NodeManager($child, $this->sessionManager);
            }

            return $children;
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function removeProperty($name)
    {
        try {
            $property = $this->node->getProperty($name);
            $property->remove();
            $this->sessionManager->save();
        }catch(VersionException $e){
            throw new InternalServerErrorException('The parent node of this property is versionable and checked-in or is
                non-versionable but its nearest versionable ancestor is checked-in and this implementation performs
                this validation immediately instead of waiting until save');
        }catch(LockException $e){
            throw new ResourceLockedException('A lock prevents the setting of the property and this implementation performs this validation
                immediately instead of waiting until save');
        }catch(ConstraintViolationException $e){
            throw new ResourceConstraintViolationException('Removing the specified property would violate a node type or implementation-specific
                constraint and this implementation performs this validation immediately instead of waiting until save');
        }catch(PHPCRAccessDeniedException $e){
            throw new AccessDeniedException('This property or an item in its subgraph is currently the target of a REFERENCE
                property located in this workspace but outside this property\'s subgraph and the current Session does not have
                read access to that REFERENCE property or if the current Session does not have sufficient privileges to
                remove the property');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function setProperty($name, $value, $type = null)
    {
        if (is_null($name) || mb_strlen($name) == 0) {
            throw new InternalServerErrorException('The property name is empty');
        }

        try{
            if (!is_null($type)) {
                $this->node->setProperty($name, $value, $type);
            } else {
                $this->node->setProperty($name, $value);
            }
            $this->sessionManager->save();
        } catch (UnsupportedRepositoryOperationException $e) {
            throw new NotSupportedOperationException('The type parameter is set and different from the current type and this implementation does not support dynamic re-binding');
        }catch(ValueFormatException $e){
            throw new NotSupportedOperationException('The specified property is a DATE but the value cannot be expressed in
                the ISO 8601-based format defined in the JCR 2.0 specification and the implementation does not support dates
                incompatible with that format or value cannot be converted to the type of the specified property or the property already exists and is multi-valued');
        }catch(LockException $e){
            throw new ResourceLockedException('A lock prevents the setting of the property and this implementation performs this validation immediately instead of waiting until save');
        } catch (ConstraintViolationException $e) {
            throw new ResourceConstraintViolationException($e->getMessage());
        }catch(VersionException $e){
            throw new InternalServerErrorException('This node is versionable and checked-in or is non-versionable but its nearest
                versionable ancestor is checked-in and this implementation performs this validation immediately instead of waiting
                until save');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function rename($newName)
    {
        if (is_null($newName) || mb_strlen($newName) == 0) {
            throw new InternalServerErrorException('The new name is empty');
        }

        try {
            $this->node->rename($newName);
            $this->sessionManager->save();
        } catch (ItemExistsException $e) {
            throw new InternalServerErrorException('There already exists a sibling node of this node with the specified name, same-name siblings are not
                allowed and this implementation performs this validation immediately');
        } catch (LockException $e) {
            throw new ResourceLockedException('A lock prevents the name change and this implementation performs this validation immediately');
        } catch (ConstraintViolationException $e) {
            throw new ResourceConstraintViolationException('A node type or implementation-specific constraint is violated and this implementation performs this validation immediately');
        } catch (VersionException $e) {
            throw new InternalServerErrorException('This node is read-only due to a checked-in node and this implementation performs this validation immediately');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function remove()
    {
        try {
            $this->node->remove();
            $this->sessionManager->save();
        } catch (PHPCRAccessDeniedException $e) {
            throw new AccessDeniedException('This node or an node in its subgraph is currently the target of a REFERENCE property located in this workspace but
                outside this item\'s subgraph and the current Session does not have read access to that REFERENCE property or if the current
                Session does not have sufficient privileges to remove the node');
        } catch (LockException $e) {
            throw new ResourceLockedException('A lock prevents the removal of this node and this implementation performs this validation immediately instead of waiting until save');
        } catch (ConstraintViolationException $e) {
            throw new ResourceConstraintViolationException('Removing the specified node would violate a node type or implementation-specific constraint and this implementation performs this validation immediately instead of waiting until save');
        } catch (VersionException $e) {
            throw new InternalServerErrorException('The parent node of this node is versionable and checked-in or is non-versionable but its nearest versionable ancestor is checked-in and this implementation performs this validation immediately instead of waiting until save');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function addNode($relPath, $primaryNodeTypeName = null)
    {
        if (is_null($relPath) || mb_strlen($relPath) == 0) {
            throw new InternalServerErrorException('The relative path is empty');
        }

        try {
            $this->node->addNode($relPath, $primaryNodeTypeName);
            $this->sessionManager->save();
        } catch (ItemExistsException $e) {
            throw new InternalServerErrorException('A node at the specified path already exists, same-name siblings are not allowed and this implementation performs this validation immediately');
        } catch (PathNotFoundException $e) {
            throw new ResourceNotFoundException('The specified path implies intermediary Nodes that do not exist or the last element of relPath has an index, and this implementation performs this validation immediately');
        } catch (ConstraintViolationException $e) {
            throw new ResourceConstraintViolationException('A node type or implementation-specific constraint is violated or if an attempt is made to add a node as the child of a property and this implementation performs this validation immediately');
        } catch (VersionException $e) {
            throw new InternalServerErrorException('The node to which the new child is being added is read-only due to a checked-in node and this implementation performs this validation immediately');
        } catch (LockException $e) {
            throw new ResourceLockedException('A lock prevents the addition of the node and this implementation performs this validation immediately instead of waiting until save');
        } catch (\InvalidArgumentException $e) {
             throw new InternalServerErrorException('The relPath is an absolute path');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }
}
