<?php

namespace PHPCRAPI\API\Tests\Manager;

use PHPCRAPI\API\Manager\SessionManager;
use PHPCRAPI\API\Manager\WorkspaceManager;
use PHPCRAPI\API\Manager\NodeManager;

class SessionManagerTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    private $repository;

    public function setUp()
    {
        $sessionInterface = $this->mock('\PHPCR\SessionInterface', null);
        $sessionInterface->mock()
            ->getNode($this->mock('\PHPCR\NodeInterface', null))
            ->getRootNode($this->mock('\PHPCR\NodeInterface', null))
            ->getNodes([$this->mock('\PHPCR\NodeInterface', null)])
            ->getNodeByIdentifier($this->mock('\PHPCR\NodeInterface', null))
            ->getWorkspace($this->mock('\PHPCR\WorkspaceInterface', null));

        $this->repository = $this->mock('\PHPCRAPI\PHPCR\Repository')
            ->login($sessionInterface)
            ->getParameters()
            ->new();
    }

    public function testItShouldCallGetFactory()
    {
        $session = $this->mock('\PHPCRAPI\PHPCR\Session')
            ->getFactory($this->once())
            ->new($this->repository, 'default');
        $manager = new SessionManager($session);
        $manager->getFactory();
    }

    public function testItShouldCallGetName()
    {
        $session = $this->mock('\PHPCRAPI\PHPCR\Session')
            ->getName($this->once())
            ->new($this->repository, 'default');
        $manager = new SessionManager($session);
        $manager->getName();
    }

    public function testItShouldCallGetWorkspace()
    {
        $session = $this->mock('\PHPCRAPI\PHPCR\Session')
            ->getWorkspace($this->mock('\PHPCRAPI\PHPCR\Workspace', null), $this->once())
            ->new($this->repository, 'default');
        $manager = new SessionManager($session);

        $this->assertTrue($manager->getWorkspaceManager() instanceof WorkspaceManager);
    }

    public function testItShouldCallGetNode()
    {
        $session = $this->mock('\PHPCRAPI\PHPCR\Session')
            ->getNode($this->mock('\PHPCRAPI\PHPCR\Node', null), $this->once())
            ->new($this->repository, 'default');
        $manager = new SessionManager($session);

        $this->assertTrue($manager->getNode('/') instanceof NodeManager);
    }

    public function testItShouldCallNodeExists()
    {
        $session = $this->mock('\PHPCRAPI\PHPCR\Session')
            ->nodeExists($this->once())
            ->new($this->repository, 'default');
        $manager = new SessionManager($session);

        $manager->nodeExists('/');
    }

    public function testItShouldCallGetRootNode()
    {
        $session = $this->mock('\PHPCRAPI\PHPCR\Session')
            ->getRootNode($this->mock('\PHPCRAPI\PHPCR\Node', null), $this->once())
            ->new($this->repository, 'default');
        $manager = new SessionManager($session);

        $this->assertTrue($manager->getRootNode() instanceof NodeManager);
    }

    public function testItShouldCallSave()
    {
        $session = $this->mock('\PHPCRAPI\PHPCR\Session')
            ->save($this->once())
            ->new($this->repository, 'default');
        $manager = new SessionManager($session);

        $manager->save();
    }

    public function testItShouldCallMove()
    {
        $session = $this->mock('\PHPCRAPI\PHPCR\Session')
            ->save($this->once())
            ->move($this->once())
            ->new($this->repository, 'default');
        $manager = new SessionManager($session);

        $manager->move('/src', '/dest');
    }
}
