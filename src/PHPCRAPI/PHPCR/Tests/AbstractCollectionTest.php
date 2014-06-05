<?php

namespace PHPCRAPI\PHPCR\Tests;


class AbstractCollectionTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    private $collection;

    public function setUp()
    {
        $this->collection = $this->mock('\PHPCRAPI\PHPCR\AbstractCollection')
            ->new();

        $this->item = $this->mock('\PHPCRAPI\PHPCR\CollectionItemInterface')
            ->getName('Test')
            ->new();
    }

    public function testItShouldAddItemToCollection()
    {
        $this->assertCount(0, $this->collection->getAll(), 'The collection should be empty');
        $this->collection->add($this->item);
        $this->assertCount(1, $this->collection->getAll(), 'The collection should have only 1 element');
        $this->assertSame($this->collection->get('Test'), $this->item, 'The collection should return the added item');
    }

    public function testItShouldRemoveItemFromCollection()
    {
        $this->collection->add($this->item);
        $this->collection->remove('Test');
        $this->assertCount(0, $this->collection->getAll(), 'The collection should be empty');
    }

    /**
     * @expectedException \PHPCRAPI\PHPCR\Exception\CollectionUnknownKeyException
     */
    public function testItShouldThrowExceptionWhenRemovingAnUnknownItemFromCollection()
    {
        $this->collection->remove('Test');
    }

    public function testItShouldReturnTrueWhenAnItemIsInTheCollection()
    {
        $this->collection->add($this->item);
        $this->assertTrue($this->collection->has('Test'), 'The collection should contain `Test` item');
    }

    public function testItShouldAddItemsPassedInTheConstructor()
    {
        $collection = $this->mock('\PHPCRAPI\PHPCR\AbstractCollection')
            ->new([$this->item]);
        $this->assertSame($collection->get('Test'), $this->item, 'The collection should return the added item');
    }
}
