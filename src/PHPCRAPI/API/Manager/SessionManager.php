<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\API\Manager;

use PHPCR\RepositoryException;
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

	public function __construct(Session $session){
		$this->session = $session;
	}

 	public function getFactory(){
        return $this->session->getFactory();
    }

	public function getName(){
		return $this->session->getName();
	}

	public function getWorkspaceManager(){
		return new WorkspaceManager($this->session->getWorkspace());
	}

	public function getNode($path){
		return new NodeManager($this->session->getNode($path));
	}

	public function nodeExists($path){
		try{
			return $this->session->nodeExists($path);
		}catch(RepositoryException $e){
			throw new InternalServerErrorException($e->getMessage());
		}
	}
	public function getRootNode(){
		return new NodeManager($this->session->getRootNode());
	}
}
