<?php

namespace PHPCRAPI\API\Tests\Manager;

use PHPCRAPI\API\Manager\WorkspaceManager;

class WorkspaceManagerTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    private $workspaceInterface;

    private $session;

    public function setUp()
    {
        $this->workspaceInterface = $this->mock('\PHPCR\WorkspaceInterface', null);
        $this->session = $this->mock('\PHPCRAPI\PHPCR\Session', null);
    }

    /**
     * @expectedException \PHPCRAPI\API\Exception\InternalServerErrorException
     */
    public function testItShouldTriggerAnExceptionIfNameIsEmptyWhenCreatingAWorkspace()
    {
        $workspace = $this->mock('\PHPCRAPI\PHPCR\Workspace')
            ->getAccessibleWorkspaceNames(['default'])
            ->new($this->workspaceInterface, $this->session);
        $manager = new WorkspaceManager($workspace);
        $manager->createWorkspace(null);
    }

    /**
     * @expectedException \PHPCRAPI\API\Exception\InternalServerErrorException
     */
    public function testItShouldTriggerAnExceptionIfTheWorkspaceAlreadyExistsWhenCreatingAWorkspace()
    {
        $workspace = $this->mock('\PHPCRAPI\PHPCR\Workspace')
            ->getAccessibleWorkspaceNames(['default'])
            ->new($this->workspaceInterface, $this->session);
        $manager = new WorkspaceManager($workspace);
        $manager->createWorkspace('default');
    }

    public function testItShouldCallCreateWorkspaceOnTheWorkspace()
    {
        $workspace = $this->mock('\PHPCRAPI\PHPCR\Workspace')
            ->getAccessibleWorkspaceNames(['default'])
            ->createWorkspace($this->once())
            ->new($this->workspaceInterface, $this->session);
        $manager = new WorkspaceManager($workspace);

        $manager->createWorkspace('security', null);
    }

    /**
     * @expectedException \PHPCRAPI\API\Exception\InternalServerErrorException
     */
    public function testItShouldTriggerAnExceptionIfNameIsEmptyWhenDeletingAWorkspace()
    {
        $workspace = $this->mock('\PHPCRAPI\PHPCR\Workspace')
            ->getAccessibleWorkspaceNames(['default'])
            ->new($this->workspaceInterface, $this->session);
        $manager = new WorkspaceManager($workspace);
        $manager->deleteWorkspace(null);
    }

    public function testItShouldCallDeleteWorkspaceOnTheWorkspace()
    {
        $workspace = $this->mock('\PHPCRAPI\PHPCR\Workspace')
            ->getAccessibleWorkspaceNames(['default'])
            ->deleteWorkspace($this->once())
            ->new($this->workspaceInterface, $this->session);
        $manager = new WorkspaceManager($workspace);

        $manager->deleteWorkspace('security');
    }

    public function testItShouldCallGetAccessibleWorkspaceNames()
    {
        $workspace = $this->mock('\PHPCRAPI\PHPCR\Workspace')
            ->getAccessibleWorkspaceNames($this->once())
            ->new($this->workspaceInterface, $this->session);
        $manager = new WorkspaceManager($workspace);

        $manager->getAccessibleWorkspaceNames();
    }
}
