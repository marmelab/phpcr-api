<?php

namespace PHPCRAPI\PHPCR\Tests\Collection;

use PHPCRAPI\PHPCR\Collection\RepositoryCollection;
use PHPCRAPI\PHPCR\Loader\RepositoryConfigurationLoader;
use PHPCRAPI\PHPCR\Factory;

class RepositoryConfigurationLoaderTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    private $collection;

    private $factoryConfigurationLoader;

    private $factory;

    public function setUp() {
        $this->factory = new Factory('foo', '\Foo', array('param'), array());
        $factoryCollection = $this->mock('\PHPCRAPI\PHPCR\Collection\FactoryCollection')
            ->get($this->factory)
            ->new();
        $this->factoryConfigurationLoader = $this->mock('\PHPCRAPI\PHPCR\Loader\FactoryConfigurationLoader')
            ->getFactories($factoryCollection)
            ->new();

        $loader = new RepositoryConfigurationLoader(array(
            'repositoryTest' => array(
                'factory' => 'foo',
                'parameters' => array('param' => 'test')
            ),
            'repositoryTest2' => array(
                'factory' => 'foo',
                'parameters' => array('param' => 'test')
            )
        ), $this->factoryConfigurationLoader);
        $this->collection = $loader->getRepositories();
    }

    public function testItShouldCreateARepositoryCollectionFilledWithOurConfiguration() {
        $this->assertTrue($this->collection instanceof RepositoryCollection, 'The loader should return a RepositoryCollection');
        $this->assertTrue($this->collection->has('repositoryTest'));
        $this->assertTrue($this->collection->has('repositoryTest2'));

        $repositoryTest = $this->collection->get('repositoryTest');
        $this->assertEquals($this->factory, $repositoryTest->getFactory());
        $this->assertEquals(array('param' => 'test'), $repositoryTest->getParameters());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testItShouldTriggerExceptionIfMissingParameter() {
        $loader = new RepositoryConfigurationLoader(array(
            'repositoryTest' => array(
                'factory' => 'foo',
                'parameters' => array()
            )
        ), $this->factoryConfigurationLoader);

        $loader->getRepositories();
    }
}
