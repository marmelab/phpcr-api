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
        try{
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
        }catch(RepositoryException $e){
            throw new ResourceNotFoundException($e->getMessage());
        }
    }

    public function setProperty($name, $value, $type = null)
    {
        if(is_null($name) || mb_strlen($name) == 0){
            throw new InternalServerErrorException('The property name is empty');
        }

        try{
            $this->node->setProperty($name, $value, $type);
            $this->sessionManager->save();
        }catch(UnsupportedRepositoryOperationException $e){
            throw new NotSupportedOperationException('The type parameter is set and different from the current type and this implementation does not support dynamic re-binding');
        }catch(ValueFormatException $e){
            throw new NotSupportedOperationException('The specified property is a DATE but the value cannot be expressed in 
                the ISO 8601-based format defined in the JCR 2.0 specification and the implementation does not support dates 
                incompatible with that format or value cannot be converted to the type of the specified property or the property already exists and is multi-valued');      
        }catch(LockException $e){
            throw new ResourceLockedException('A lock prevents the setting of the property and this implementation performs this validation immediately instead of waiting until save');
        }catch(ConstraintViolationException $e){
            throw new ResourceConstraintViolationException($e->getMessage());
        }catch(RepositoryException $e){
            throw new ResourceNotFoundException($e->getMessage());
        }catch(VersionException $e){
            throw new InternalServerErrorException('This node is versionable and checked-in or is non-versionable but its nearest 
                versionable ancestor is checked-in and this implementation performs this validation immediately instead of waiting 
                until save');
        }catch(\InvalidArgumentException $e){
            throw new InternalServerErrorException($e->getMessage());
        }
    }
}
