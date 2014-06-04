<?php

namespace PHPCRAPI\PHPCR\Tests\Collection;

use PHPCRAPI\PHPCR\Collection\FactoryCollection;
use PHPCRAPI\PHPCR\Loader\FactoryConfigurationLoader;

class FactoryConfigurationLoaderTest extends \PHPUnit_Framework_TestCase
{
    use \Xpmock\TestCaseTrait;

    private $collection;

    public function setUp() {
        $loader = new FactoryConfigurationLoader(array(
            'foo' => array(
                'class' => '\Foo',
                'parameters' => array('blob'),
                'supportedOperations' => array('write')
            ),
            'bar' => array(
                'class' => '\Bar',
                'parameters' => array('blobi'),
                'supportedOperations' => array('read')
            )
        ));
        $this->collection = $loader->getFactories();
    }

    public function testItShouldCreateAFactoryCollectionFilledWithOurConfiguration() {
        $this->assertTrue($this->collection instanceof FactoryCollection, 'The loader should return a FactoryCollection');
        $this->assertTrue($this->collection->has('foo'));
        $this->assertTrue($this->collection->has('bar'));

        $foo = $this->collection->get('foo');
        $this->assertEquals('\Foo', $foo->getClass());
        $this->assertEquals(array('blob'), $foo->getParameters());
        $this->assertEquals(array('write'), $foo->getSupportedOperations());

        $bar = $this->collection->get('bar');
        $this->assertEquals('\Bar', $bar->getClass());
        $this->assertEquals(array('blobi'), $bar->getParameters());
        $this->assertEquals(array('read'), $bar->getSupportedOperations());
    }
}
