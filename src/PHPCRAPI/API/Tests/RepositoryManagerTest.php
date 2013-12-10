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
use PHPCRAPI\API\Manager\SessionManager;
use PHPCRAPI\PHPCR\Collection\RepositoryCollection;

class RepositoryManagerTest extends \PHPUnit_Framework_TestCase
{
    private static $loader;

    private static $repositoryName;

    public static function setUpBeforeClass()
    {
        self::$repositoryName = sprintf('Repository %s',uniqid());

        $repositoriesConfiguration = array(
            self::$repositoryName	=>	array(
                'factory'		=>	'jackalope.jackrabbit',
                'parameters'	=>	array(
                    'jackalope.jackrabbit_uri'		=> 'http://localhost:8080/server',
                    'credentials.username'			=>	'admin',
                    'credentials.password'			=> 	'admin'
            ))
        );

        self::$loader = new RepositoryLoader($repositoriesConfiguration);
    }

    public function testRepositoriesLoading()
    {
        $repositories = self::$loader->getRepositories();

        $this->assertTrue($repositories instanceof RepositoryCollection);
           $this->assertTrue($repositories->has(self::$repositoryName));
       }

    public function testLoginOnDefaultWorkspace()
    {
        $repositoryManager = new RepositoryManager(self::$loader->getRepositories()->get(self::$repositoryName));
        $sessionManager = $repositoryManager->getSessionManager();
        $this->assertTrue($sessionManager instanceof SessionManager);

        return $sessionManager;
    }
}
