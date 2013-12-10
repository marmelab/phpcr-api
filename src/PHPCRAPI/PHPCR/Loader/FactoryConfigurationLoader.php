<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\PHPCR\Loader;

use PHPCRAPI\PHPCR\Collection\FactoryCollection;
use PHPCRAPI\PHPCR\Factory;

class FactoryConfigurationLoader
{
	private $factories;

	public function __construct($factoriesConfiguration){
		$this->factories = function() use($factoriesConfiguration){
			$factories = new FactoryCollection();
			foreach($factoriesConfiguration as $name=>$factory){
				$factories->add(new Factory(
					$name, 
					$factory['class'], 
					(array)$factory['parameters'],
					(array)$factory['supportedOperations']
				));
			}
			return $factories;
		};
	}

	public function getFactories(){
		if($this->factories instanceof \Closure){
			$c = $this->factories;
			$this->factories = $c();
		}

		return $this->factories;
	}
}
