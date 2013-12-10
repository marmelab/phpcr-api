<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\API\Manager;

use PHPCR\LoginException;
use PHPCR\NoSuchWorkspaceException;
use PHPCR\RepositoryException;
use PHPCRAPI\API\Exception\ResourceNotFoundException;
use PHPCRAPI\API\Exception\AccessDeniedException;
use PHPCRAPI\API\Exception\GatewayTimeoutException;
use PHPCRAPI\API\Exception\InternalServerErrorException;
use PHPCRAPI\PHPCR\Repository;
use PHPCRAPI\PHPCR\Session;

/**
 * Available function for repository management by the API
 *
 * @api
 */
class RepositoryManager
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getFactory()
    {
        return $this->repository->getFactory();
    }

    public function getName()
    {
        return $this->repository->getName();
    }

    public function getSessionManager($workspace = null)
    {
        try {
            $session = new Session(
                $this->repository,
                $workspace
            );

            return new SessionManager($session);
        } catch (NoSuchWorkspaceException $e) {
            if (!is_null($workspace)) {
                throw new ResourceNotFoundException('The workspace does not exist');
            } else {
                throw new GatewayTimeoutException('The repository is not available');
            }
        } catch (LoginException $e) {
            throw new AccessDeniedException('The session does not have sufficient access to open the workspace');
        } catch (RepositoryException $e) {
            throw new InternalServerErrorException($e->getMessage());
        }
    }
}
