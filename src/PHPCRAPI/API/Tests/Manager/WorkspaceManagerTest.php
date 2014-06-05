<?php

namespace PHPCRAPI\API\Tests\Manager;

use PHPCRAPI\API\Manager\WorkspaceManager;

class WorkspaceManagerTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    private $workspace;

    private $manager;

    public function setUp()
    {
        $this->workspace = $this->mock('\PHPCRAPI\PHPCR\Workspace')
            ->getAccessibleWorkspaceNames(['default'])
            ->new($this->mock('\PHPCR\WorkspaceInterface', null), $this->mock('\PHPCRAPI\PHPCR\Session', null));
        $this->manager = new WorkspaceManager($this->workspace);
    }

    /**
     * @expectedException \PHPCRAPI\API\Exception\InternalServerErrorException
     */
    public function testItShouldTriggerAnExceptionIfNameIsEmptyWhenCreatingAWorkspace()
    {
        $this->manager->createWorkspace(null);
    }

    /**
     * @expectedException \PHPCRAPI\API\Exception\InternalServerErrorException
     */
    public function testItShouldTriggerAnExceptionIfTheWorkspaceAlreadyExistsWhenCreatingAWorkspace()
    {
        $this->manager->createWorkspace('default');
    }

    public function testItShouldCallCreateWorkspaceOnTheWorkspace()
    {
        $workspace = $this->mock('\PHPCRAPI\PHPCR\Workspace')
            ->getAccessibleWorkspaceNames(['default'])
            ->createWorkspace($this->once())
            ->new($this->mock('\PHPCR\WorkspaceInterface', null), $this->mock('\PHPCRAPI\PHPCR\Session', null));
        $manager = new WorkspaceManager($workspace);

        $manager->createWorkspace('security', null);
    }

    /**
     * @expectedException \PHPCRAPI\API\Exception\InternalServerErrorException
     */
    public function testItShouldTriggerAnExceptionIfNameIsEmptyWhenDeletingAWorkspace()
    {
        $this->manager->deleteWorkspace(null);
    }

    public function testItShouldCallDeleteWorkspaceOnTheWorkspace()
    {
        $workspace = $this->mock('\PHPCRAPI\PHPCR\Workspace')
            ->getAccessibleWorkspaceNames(['default'])
            ->deleteWorkspace($this->once())
            ->new($this->mock('\PHPCR\WorkspaceInterface', null), $this->mock('\PHPCRAPI\PHPCR\Session', null));
        $manager = new WorkspaceManager($workspace);

        $manager->deleteWorkspace('security');
    }
}
