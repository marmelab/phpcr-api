<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\API\Manager;

use PHPCRAPI\PHPCR\Workspace;
use PHPCRAPI\API\Exception\AccessDeniedException;
use PHPCRAPI\API\Exception\InternalServerErrorException;
use PHPCRAPI\API\Exception\NotSupportedOperationException;
use PHPCRAPI\API\Exception\ResourceNotFoundException;
use PHPCR\ItemExistsException;
use PHPCR\RepositoryException;
use PHPCR\NoSuchWorkspaceException;
use PHPCR\UnsupportedRepositoryOperationException;

/**
 * Available function for workspace management by the API
 *
 * @api
 */
class WorkspaceManager
{
    private $workspace;

    public function __construct(Workspace $workspace)
    {
        $this->workspace = $workspace;
    }

    public function getAccessibleWorkspaceNames()
    {
        try {
            return $this->workspace->getAccessibleWorkspaceNames();
        } catch (RepositoryException $e) {
            throw new ResourceNotFoundException($e->getMessage());
        }
    }

    public function createWorkspace($name, $srcWorkspace = null)
    {
        if (is_null($name) || mb_strlen($name) == 0) {
            throw new InternalServerErrorException('The workspace name is empty');
        } elseif (in_array($name, $this->workspace->getAccessibleWorkspaceNames())) {
            throw new InternalServerErrorException('The workspace already exists');
        }

        try {
            $this->workspace->createWorkspace($name, $srcWorkspace);
        } catch (\PHPCR\AccessDeniedException $e) {
            throw new AccessDeniedException('The session through which this workspace object was acquired does not have sufficient access to create the new workspace');
        } catch (UnsupportedRepositoryOperationException $e) {
            if (is_null($srcWorkspace)) {
                throw new NotSupportedOperationException('The repository does not support the creation of workspaces');
            } else {
                throw new NotSupportedOperationException('The repository does not support the cloning of workspaces');
            }
        } catch (NoSuchWorkspaceException $e) {
            throw new ResourceNotFoundException('The source workspace does not exist');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }

    public function deleteWorkspace($name)
    {
        if (is_null($name) || mb_strlen($name) == 0) {
            throw new InternalServerErrorException('The workspace name is empty');
        }

        try {
            $this->workspace->deleteWorkspace($name);
        } catch (\PHPCR\AccessDeniedException $e) {
            throw new AccessDeniedException('The session through which this workspace object was acquired does not have sufficient access to remove the workspace');
        } catch (UnsupportedRepositoryOperationException $e) {
            throw new NotSupportedOperationException('The repository does not support the removal of workspaces');
        } catch (NoSuchWorkspaceException $e) {
            throw new ResourceNotFoundException('The workspace does not exist');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }
}
