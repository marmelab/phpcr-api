<?php

namespace PHPCRAPI\PHPCR\Tests;

use PHPCRAPI\PHPCR\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    public function testItShouldInstanciateTheFactory() {
        $repositoryInterface = $this->mock()
            ->fooBar()
            ->new();

        $factoryInstance = $this->mock()
            ->getRepository($repositoryInterface)
            ->new();

        $factory = $this->mock('\PHPCRAPI\PHPCR\Factory')
            ->instanciate($factoryInstance, $this->once())
            ->new();

        $repository = new Repository('Repository Test', $factory, array('param' => 'value'));
        $repository->fooBar(); // Will trigger Repository::getRepository()
        $repository->fooBar(); // Will trigger Repository::getRepository() and it should not call Factory::instanciate
    }
}