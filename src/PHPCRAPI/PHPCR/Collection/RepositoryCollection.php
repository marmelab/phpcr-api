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
use PHPCRAPI\PHPCR\Repository;

/**
 * RepositoryCollection provides collection management for Repository
 *
 * @api
 */
class RepositoryCollection extends AbstractCollection
{
    public function add(CollectionItemInterface $repository)
    {
    	if(!($repository instanceof Repository)){
    		throw new \IllegalArgumentException('You can only add Repository object to this collection');
    	}

        parent::add($repository);
        return $this;
    }
}
