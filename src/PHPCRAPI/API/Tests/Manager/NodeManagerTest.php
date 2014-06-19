<?php

namespace PHPCRAPI\API\Tests\Manager;

use PHPCRAPI\API\Manager\NodeManager;

class NodeManagerTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    private $sessionManager;

    private $nodeInterface;

    public function setUp()
    {
        $this->sessionManager = $this->mock('\PHPCRAPI\API\Manager\SessionManager', null);
        $this->nodeInterface = $this->mock('\PHPCR\NodeInterface', null);
    }

    public function testItShouldCallGetReducedTree()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->getReducedTree($this->once())
            ->new($this->nodeInterface);

        $manager = new NodeManager($node, $this->sessionManager);
        $manager->getReducedTree();
    }

    public function testItShouldCallGetName()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->getName($this->once())
            ->new($this->nodeInterface);

        $manager = new NodeManager($node, $this->sessionManager);
        $manager->getName();
    }

    public function testItShouldCallGetParent()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->getParent($this->mock('\PHPCRAPI\PHPCR\Node', null), $this->once())
            ->new($this->nodeInterface);

        $manager = new NodeManager($node, $this->sessionManager);
        $this->assertTrue($manager->getParent() instanceof NodeManager);
    }

    public function testItShouldCallGetPath()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->getPath($this->once())
            ->new($this->nodeInterface);

        $manager = new NodeManager($node, $this->sessionManager);
        $manager->getPath();
    }

    public function testItShouldCallGetPropertiesAsArray()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->getPropertiesAsArray($this->once())
            ->new($this->nodeInterface);

        $manager = new NodeManager($node, $this->sessionManager);
        $manager->getPropertiesAsArray();
    }

    public function testItShouldCallGetNodes()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->getNodes([$this->mock('\PHPCRAPI\PHPCR\Node', null)], $this->once())
            ->new($this->nodeInterface);

        $manager = new NodeManager($node, $this->sessionManager);
        $this->assertTrue($manager->getChildren()[0] instanceof NodeManager);
    }

    public function testItShouldCallHasChildren()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->hasNodes($this->once())
            ->new($this->nodeInterface);

        $manager = new NodeManager($node, $this->sessionManager);
        $manager->hasChildren();
    }

    public function testItShouldCallGetPropertyAndThenRemoveAndFinallySave()
    {
        $property = $this->mock()
            ->remove($this->once())
            ->new();

        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->getProperty($property, $this->once())
            ->new($this->nodeInterface);

        $sessionManager = $this->mock('\PHPCRAPI\API\Manager\SessionManager')
            ->save($this->once())
            ->new();

        $manager = new NodeManager($node, $sessionManager);
        $manager->removeProperty('test');
    }

    /**
     * @expectedException \PHPCRAPI\API\Exception\InternalServerErrorException
     */
    public function testItShouldTriggerIfNameIsEmptyWhenCallingSetProperty()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')->new($this->nodeInterface);
        $manager = new NodeManager($node, $this->sessionManager);
        $manager->setProperty(null, null);
    }

    public function testItShouldCallSetPropertyAndFinallySave()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->setProperty($this->once())
            ->new($this->nodeInterface);

        $sessionManager = $this->mock('\PHPCRAPI\API\Manager\SessionManager')
            ->save($this->once())
            ->new();

        $manager = new NodeManager($node, $sessionManager);
        $manager->setProperty('test', 'val');
    }

    /**
     * @expectedException \PHPCRAPI\API\Exception\InternalServerErrorException
     */
    public function testItShouldTriggerIfNameIsEmptyWhenCallingRename()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')->new($this->nodeInterface);
        $manager = new NodeManager($node, $this->sessionManager);
        $manager->rename(null);
    }

    public function testItShouldCallRenameAndFinallySave()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->rename($this->once())
            ->new($this->nodeInterface);

        $sessionManager = $this->mock('\PHPCRAPI\API\Manager\SessionManager')
            ->save($this->once())
            ->new();

        $manager = new NodeManager($node, $sessionManager);
        $manager->rename('test');
    }

    public function testItShouldCallRemoveAndFinallySave()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->remove($this->once())
            ->new($this->nodeInterface);

        $sessionManager = $this->mock('\PHPCRAPI\API\Manager\SessionManager')
            ->save($this->once())
            ->new();

        $manager = new NodeManager($node, $sessionManager);
        $manager->remove();
    }

    /**
     * @expectedException \PHPCRAPI\API\Exception\InternalServerErrorException
     */
    public function testItShouldTriggerIfRelPathIsEmptyWhenCallingAddNode()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')->new($this->nodeInterface);
        $manager = new NodeManager($node, $this->sessionManager);
        $manager->addNode(null);
    }

    public function testItShouldCallAddNodeAndFinallySave()
    {
        $node = $this->mock('\PHPCRAPI\PHPCR\Node')
            ->addNode($this->once())
            ->new($this->nodeInterface);

        $sessionManager = $this->mock('\PHPCRAPI\API\Manager\SessionManager')
            ->save($this->once())
            ->new();

        $manager = new NodeManager($node, $sessionManager);
        $manager->addNode('/test');
    }
}
