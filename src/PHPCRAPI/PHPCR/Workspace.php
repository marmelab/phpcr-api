<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\PHPCR;

use PHPCR\WorkspaceInterface;
/**
 * Workspace is a wrapper for a PHPCR Workspace
 * Helps us to add metadata to it and provides more control possibilities
 *
 * @api
 */
class Workspace
{
    /**
     * PHPCR Workspace the workspace refers to
     *
     * @var \PHPCR\WorkspaceInterface $workspace The PHPCR Workspace
     */
    private $workspace;

    /**
     * Session the workspace refers to
     *
     * @var Sesion $session The session
     */
    private $session;

    /**
     * Workspace constructor
     *
     * @param Repository $repository The repository to log in
     * @param string $workspaceName The workspace's name
     *
     * @api
     */
    public function __construct(WorkspaceInterface $workspace, Session $session)
    {
        $this->session = $session;
        $this->workspace = $workspace;
    }

    /**
    * @see \PHPCR\WorkspaceInterface::getSession
    */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Call bridge with the Workspace
     *
     * @param string $funcName Function's name
     * @param array  $args     Function's arguments
     */
    public function __call($funcName, $args)
    {
        return call_user_func_array(array($this->workspace, $funcName), $args);
    }
}
