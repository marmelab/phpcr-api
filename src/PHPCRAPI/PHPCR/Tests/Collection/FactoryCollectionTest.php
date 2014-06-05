<?php

namespace PHPCRAPI\PHPCR\Tests\Collection;

use PHPCRAPI\PHPCR\Collection\FactoryCollection;
use PHPCRAPI\PHPCR\Factory;

class FactoryCollectionTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    private $collection;

    public function setUp()
    {
        $this->collection = new FactoryCollection();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testItShouldOnlyAcceptFactoryObject()
    {
        $item = new Factory('Foo', 'FooBar', array(), array());
        $this->collection->add($item);
        $this->assertSame($this->collection->get('Foo'), $item, 'The collection should return the added item');

        $item2 = $this->mock('\PHPCRAPI\PHPCR\CollectionItemInterface')
            ->getName('Test')
            ->new();
        $this->collection->add($item2);
    }
}
