<?php

namespace PHPCRAPI\API\Tests;

use PHPCRAPI\PHPCR\Collection\RepositoryCollection;
use PHPCRAPI\API\RepositoryLoader;

class RepositoryLoaderTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    private $collection;

    public function setUp() {

        $loader = new RepositoryLoader([
            'repositoryJackrabbit' => [
                'factory' => 'jackalope.jackrabbit',
                'parameters' => [
                    'jackalope.jackrabbit_uri' => 'http://localhost:8000',
                    'credentials.username'     => 'admin',
                    'credentials.password'     => 'admin'
                ]
            ],
            'repositoryDoctrine' => [
                'factory' => 'jackalope.doctrine-dbal',
                'parameters' => [
                    'jackalope.doctrine_dbal_connection' => null,
                    'credentials.username'     => 'admin',
                    'credentials.password'     => 'admin'
                ]
            ]
        ]);
        $this->collection = $loader->getRepositories();
    }

    public function testItShouldCreateARepositoryCollectionFilledWithOurConfiguration() {
        $this->assertTrue($this->collection instanceof RepositoryCollection, 'The loader should return a RepositoryCollection');
        $this->assertTrue($this->collection->has('repositoryJackrabbit'));
        $this->assertTrue($this->collection->has('repositoryDoctrine'));
    }
}
