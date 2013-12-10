<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\API\Tests;

use PHPCRAPI\API\RepositoryLoader;
use PHPCRAPI\API\Manager\RepositoryManager;
use PHPCRAPI\API\Manager\WorkspaceManager;
use PHPCRAPI\PHPCR\Session;

class WorkspaceManagerTest extends \PHPUnit_Framework_TestCase
{
	public function testWorkspaceCreation(){
		$repositoriesConfiguration = array(
        	'RepoTest'	=>	array(
        		'factory'		=>	'jackalope.jackrabbit',
        		'parameters'	=>	array(
		            'jackalope.jackrabbit_uri'		=> 'http://localhost:8080/server',
		            'credentials.username'			=>	'admin',
		            'credentials.password'			=> 	'admin'
		    ))
        );

        $loader = new RepositoryLoader($repositoriesConfiguration);
        $repositoryManager = new RepositoryManager($loader->getRepositories()->get('RepoTest'));
        $sessionManager = $repositoryManager->getSessionManager();
        $workspaceManager = $sessionManager->getWorkspaceManager();

        $this->assertTrue($workspaceManager instanceof WorkspaceManager);

        $name = sprintf('Wk'.uniqid());
        $workspaceManager->createWorkspace($name);

        $this->assertContains($name, $workspaceManager->getAccessibleWorkspaceNames());
	}
}
