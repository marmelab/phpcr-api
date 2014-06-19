<?php

namespace PHPCRAPI\API\Tests\Manager;

use PHPCRAPI\API\Manager\RepositoryManager;
use PHPCRAPI\API\Manager\SessionManager;

class RepositoryManagerTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    public function testItShouldReturnASessionManager()
    {
        $sessionInterface = $this->mock('\PHPCR\SessionInterface', null);
        $repositoryInterface = $this->mock('\PHPCR\RepositoryInterface', null);

        $repository = $this->mock('\PHPCRAPI\PHPCR\Repository')
            ->login($sessionInterface)
            ->getRepository($repositoryInterface)
            ->getParameters([
                'credentials.username' => 'admin',
                'credentials.password' => 'admin'
            ])
            ->new();

        $manager = new RepositoryManager($repository);
        $this->assertTrue($manager->getSessionManager() instanceof SessionManager);
    }

    public function testItShouldCallGetFactory()
    {
        $repository = $this->mock('\PHPCRAPI\PHPCR\Repository')
            ->getFactory($this->once())
            ->new();
        $manager = new RepositoryManager($repository);
        $manager->getFactory();
    }

    public function testItShouldCallGetName()
    {
        $repository = $this->mock('\PHPCRAPI\PHPCR\Repository')
            ->getName($this->once())
            ->new();
        $manager = new RepositoryManager($repository);
        $manager->getName();
    }
}
