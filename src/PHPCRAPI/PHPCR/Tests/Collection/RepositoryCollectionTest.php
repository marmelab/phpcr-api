<?php

namespace PHPCRAPI\PHPCR\Tests\Collection;

use PHPCRAPI\PHPCR\Collection\RepositoryCollection;
use PHPCRAPI\PHPCR\Factory;
use PHPCRAPI\PHPCR\Repository;

class RepositoryCollectionTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    private $collection;

    public function setUp()
    {
        $this->collection = new RepositoryCollection();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testItShouldOnlyAcceptRepositoryObject()
    {
        $factory = new Factory('Foo', 'FooBar', array(), array());
        $item = new Repository('Foo', $factory, array());
        $this->collection->add($item);
        $this->assertSame($this->collection->get('Foo'), $item, 'The collection should return the added item');

        $item2 = $this->mock('\PHPCRAPI\PHPCR\CollectionItemInterface')
            ->getName('Test')
            ->new();
        $this->collection->add($item2);
    }
}
