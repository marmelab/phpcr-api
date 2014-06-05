<?php

namespace PHPCRAPI\PHPCR\Tests;

use PHPCRAPI\PHPCR\Node;
use PHPCRAPI\PHPCR\Session;
use PHPCRAPI\PHPCR\Workspace;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    private $sessionInterface;

    public function setUp() {
        $this->sessionInterface = $this->mock('\PHPCR\SessionInterface', null);
        $this->sessionInterface->mock()
            ->getNode($this->mock('\PHPCR\NodeInterface', null))
            ->getRootNode($this->mock('\PHPCR\NodeInterface', null))
            ->getNodes([$this->mock('\PHPCR\NodeInterface', null)])
            ->getNodeByIdentifier($this->mock('\PHPCR\NodeInterface', null))
            ->getWorkspace($this->mock('\PHPCR\WorkspaceInterface', null));
    }

    public function testItShouldCallLoginOnRepository() {
        $repository = $this->mock('\PHPCRAPI\PHPCR\Repository')
            ->login($this->sessionInterface, $this->once())
            ->getParameters()
            ->new();

        $session = new Session($repository, 'default');
    }

    public function testItShouldWrapEachInterfaceIntoANewProxy() {
        $repository = $this->mock('\PHPCRAPI\PHPCR\Repository')
            ->login($this->sessionInterface)
            ->getParameters()
            ->new();

        $session = new Session($repository, 'default');

        $this->assertTrue($session->getNode('/') instanceof Node);
        $this->assertTrue($session->getRootNode() instanceof Node);
        $this->assertTrue($session->getNodes()[0] instanceof Node);
        $this->assertTrue($session->getNodeByIdentifier('/') instanceof Node);
        $this->assertTrue($session->getWorkspace() instanceof Workspace);
    }
}
