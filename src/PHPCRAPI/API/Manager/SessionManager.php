<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\API\Manager;

use PHPCR\AccessDeniedException as PHPCRAccessDeniedException;
use PHPCR\RepositoryException;
use PHPCR\Version\VersionException;
use PHPCR\Lock\LockException;
use PHPCR\NodeType\ConstraintViolationException;
use PHPCR\NodeType\NoSuchNodeTypeException;
use PHPCR\ReferentialIntegrityException;
use PHPCR\InvalidItemStateException;
use PHPCR\ItemExistsException;
use PHPCR\PathNotFoundException;
use PHPCRAPI\API\Exception\ResourceNotFoundException;
use PHPCRAPI\API\Exception\ResourceLockedException;
use PHPCRAPI\API\Exception\ResourceConstraintViolationException;
use PHPCRAPI\API\Exception\AccessDeniedException;
use PHPCRAPI\API\Exception\InternalServerErrorException;
use PHPCRAPI\PHPCR\Session;

/**
 * Available function for session management by the API
 *
 * @api
 */
class SessionManager
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

     public function getFactory()
     {
        return $this->session->getFactory();
    }

    public function getName()
    {
        return $this->session->getName();
    }

    public function getWorkspaceManager()
    {
        return new WorkspaceManager($this->session->getWorkspace());
    }

    public function getNode($path)
    {
        try {
            return new NodeManager($this->session->getNode($path), $this);
        } catch (PathNotFoundException $e) {
            throw new ResourceNotFoundException('No accessible node is found at the specified path');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function getNodeByIdentifier($id)
    {
        try {
            return new NodeManager($this->session->getNodeByIdentifier($id), $this);
        } catch (ItemNotFoundException $e) {
            throw new ResourceNotFoundException('No node with the specified identifier exists or if this Session does not have read access to the node with the specified identifier');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function nodeExists($path)
    {
        try {
            return $this->session->nodeExists($path);
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function getRootNode()
    {
        try{
            return new NodeManager($this->session->getRootNode(), $this);
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function save()
    {
        try {
            return $this->session->save();
        }catch(PHPCRAccessDeniedException $e){
            throw new AccessDeniedException('Any of the changes to be persisted would violate the access privileges of the
                this Session. Also thrown if any of the changes to be persisted would cause the removal of a node that is
                currently referenced by a REFERENCE property that this Session does not have read access to');
        }catch(ItemExistsException $e){
            throw new InternalServerErrorException('Any of the changes to be persisted would be prevented by the
                presence of an already existing item in the workspace');
        }catch(ConstraintViolationException $e){
            throw new ResourceConstraintViolationException('Any of the changes to be persisted would be prevented by the
                presence of an already existing item in the workspace');
        }catch(InvalidItemStateException $e){
            throw new InternalServerErrorException('Any of the changes to be persisted conflicts with a change already
                persisted through another session and the implementation is such that this conflict can only be detected
                at save-time and therefore was not detected earlier, at change-time');
        }catch(ReferentialIntegrityException $e){
            throw new ResourceConstraintViolationException('Any of the changes to be persisted would cause the removal of a
                node that is currently referenced by a REFERENCE property that this Session has read access to');
        }catch(VersionException $e){
            throw new InternalServerErrorException('The save would make a result in a change to persistent storage that would
                violate the read-only status of a checked-in node');
        } catch (LockException $e) {
            throw new ResourceLockedException('The save would result in a change to persistent storage that would violate a lock');
        } catch (NoSuchNodeTypeException $e) {
            throw new ResourceNotFoundException('The save would result in the addition of a node with an unrecognized node type');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function move($srcAbsPath, $destAbsPath)
    {
        try {
            $this->session->move($srcAbsPath, $destAbsPath);
            $this->save();
        } catch(ItemExistsException $e){
            throw new InternalServerErrorException('A node already exists at destAbsPath and same-name siblings are not allowed');
        } catch (VersionException $e) {
            throw new InternalServerErrorException('The parent node of destAbsPath or the parent node of srcAbsPath is versionable and checked-in, or or is non-versionable and its nearest versionable ancestor is checked-in and this implementation performs this validation immediately');
        } catch (PathNotFoundException $e) {
            throw new ResourceNotFoundException('Either srcAbsPath or destAbsPath cannot be found and this implementation performs this validation immediatel');
        } catch(ConstraintViolationException $e){
            throw new ResourceConstraintViolationException('a node-type or other constraint violation is detected immediately and this implementation performs this validation immediately');
        } catch (LockException $e) {
            throw new ResourceLockedException('The move operation would violate a lock and this implementation performs this validation immediately');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }
}
