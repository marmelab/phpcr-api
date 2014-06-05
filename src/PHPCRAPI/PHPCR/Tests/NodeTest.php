<?php

namespace PHPCRAPI\PHPCR\Tests;

use PHPCRAPI\PHPCR\Node;

class NodeTest extends \PHPUnit_Framework_TestCase
{
	use \Xpmock\TestCaseTrait;

	private $phpcrNode;

	private $node;

	public function setUp() {
		$this->phpcrNode = $this->mock('\PHPCR\NodeInterface', null);
		$this->phpcrNode->mock()
			->getNode($this->mock('\PHPCR\NodeInterface', null))
			->getNodes([$this->mock('\PHPCR\NodeInterface', null)])
			->getParent($this->mock('\PHPCR\NodeInterface', null));

		$this->node = new Node($this->phpcrNode);
	}

	public function testItShouldWrapEachNodeInterfaceIntoANewNodeProxy() {
		$this->assertTrue($this->node->getParent() instanceof Node);
		$this->assertTrue($this->node->getNodes()[0] instanceof Node);
		$this->assertTrue($this->node->getNode('/path') instanceof Node);
	}

	public function testItShouldGenerateAReducedTree() {
		/**
		 * We create a test tree
		 *
		 * / -----
		 *     | child1
		 *     | child2 -----
		 *                | subchild
		 *
		 *
		 */

		$subchildInterface = $this->mock('\PHPCR\NodeInterface', null);

		$subchildInterface->mock()
			->getPath('/child1/subchild')
			->getNodes([])
			->hasNodes(false)
			->getName('subchild');

		$subchild = new Node($subchildInterface);

		$child1Interface = $this->mock('\PHPCR\NodeInterface', null);

		$child1Interface->mock()
			->getPath('/child1')
			->getNodes([$subchildInterface])
			->hasNodes(true)
			->getName('child1');

		$child1 = new Node($child1Interface);

		$child2Interface = $this->mock('\PHPCR\NodeInterface', null);

		$child2Interface->mock()
			->getPath('/child2')
			->getNodes([])
			->hasNodes(false)
			->getName('child2');

		$child2 = new Node($child2Interface);

		$rootNodeInterface = $this->mock('\PHPCR\NodeInterface', null);

		$rootNodeInterface->mock()
			->getPath('/')
			->getNodes([$child1Interface, $child2Interface])
			->hasNodes(true)
			->getName('/');

		$rootNode = new Node($rootNodeInterface);

		$subchildInterface->mock()->getParent($child1Interface);
		$child1Interface->mock()->getParent($rootNodeInterface);
		$child2Interface->mock()->getParent($rootNodeInterface);



		$reducedTreeExpectedForSubchild = [
			'/' => [
				'name' => '/',
				'path' => '/',
				'hasChildren' => true,
				'children' => [
					0 => [
						'name' => 'child1',
						'path' => '/child1',
						'hasChildren'=> true,
						'children' => [
							0 => [
								'name' => 'subchild',
								'path' => '/child1/subchild',
								'hasChildren'=> false,
								'children' => []
							]
						]
					],

					1 => [
						'name' => 'child2',
						'path' => '/child2',
						'hasChildren'=> false,
						'children' => []
					]
				]
			]
		];

		$this->assertEquals($reducedTreeExpectedForSubchild, $subchild->getReducedTree());

		$reducedTreeExpectedForChild2 = [
			'/' => [
				'name' => '/',
				'path' => '/',
				'hasChildren' => true,
				'children' => [
					0 => [
						'name' => 'child1',
						'path' => '/child1',
						'hasChildren'=> true,
						'children' => []
					],

					1 => [
						'name' => 'child2',
						'path' => '/child2',
						'hasChildren'=> false,
						'children' => []
					]
				]
			]
		];

		$this->assertEquals($reducedTreeExpectedForChild2, $child2->getReducedTree());

		$reducedTreeExpectedForChild1 = [
			'/' => [
				'name' => '/',
				'path' => '/',
				'hasChildren' => true,
				'children' => [
					0 => [
						'name' => 'child1',
						'path' => '/child1',
						'hasChildren'=> true,
						'children' => [
							0 => [
								'name' => 'subchild',
								'path' => '/child1/subchild',
								'hasChildren'=> false,
								'children' => []
							]
						]
					],

					1 => [
						'name' => 'child2',
						'path' => '/child2',
						'hasChildren'=> false,
						'children' => []
					]
				]
			]
		];

		$this->assertEquals($reducedTreeExpectedForChild1, $child1->getReducedTree());

		$reducedTreeExpectedForRoot = [
			'/' => [
				'name' => '/',
				'path' => '/',
				'hasChildren' => true,
				'children' => [
					0 => [
						'name' => 'child1',
						'path' => '/child1',
						'hasChildren'=> true,
						'children' => []
					],

					1 => [
						'name' => 'child2',
						'path' => '/child2',
						'hasChildren'=> false,
						'children' => []
					]
				]
			]
		];

		$this->assertEquals($reducedTreeExpectedForRoot, $rootNode->getReducedTree());
	}
}
