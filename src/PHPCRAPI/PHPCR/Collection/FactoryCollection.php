<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\PHPCR\Collection;

use PHPCRAPI\PHPCR\AbstractCollection;
use PHPCRAPI\PHPCR\CollectionItemInterface;
use PHPCRAPI\PHPCR\Factory;

/**
 * FactoryCollection provides collection management for Factory
 *
 * @api
 */
class FactoryCollection extends AbstractCollection
{
    public function add(CollectionItemInterface $factory)
    {
    	if(!($factory instanceof Factory)){
    		throw new \IllegalArgumentException('You can only add Factory object to this collection');
    	}

        parent::add($factory);
        return $this;
    }
}
