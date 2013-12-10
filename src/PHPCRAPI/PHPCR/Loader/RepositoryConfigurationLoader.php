<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\PHPCR\Loader;

use PHPCRAPI\PHPCR\Collection\RepositoryCollection;
use PHPCRAPI\PHPCR\Repository;

/**
 * The RepositoryConfigurationLoader give access to all declared repositories in configuration.
 *
 * It wraps them into the custom Repository class to add metadata to it and get better control.
 *
 * @api
 */
class RepositoryConfigurationLoader
{
    private $repositories;

    /**
     * Service constructor
     *
     * @param array $repositories The repositories configuration
     * @param array $factories The factories configuration
     */
    public function __construct($repositoriesConfiguration, FactoryConfigurationLoader $factoryLoader)
    {
        $this->repositories = function() use($repositoriesConfiguration, $factoryLoader){
            $factories = $factoryLoader->getFactories();
            $repositories = new RepositoryCollection();

            foreach ($repositoriesConfiguration as $name=>$repository){
                $factory = $factories->get($repository['factory']);
                if(count(array_intersect(
                        array_keys($repository['parameters']),
                        $factory->getParameters())
                    ) != count($factory->getParameters())){
                    throw new \InvalidArgumentException('An error occurred during repositories parsing : missing parameters');
                }

                $repositories->add(new Repository(
                    $name, 
                    $factory,
                    (array) $repository['parameters']
                ));
            }

            return $repositories;
        };
    }

    public function getRepositories()
    {
        if($this->repositories instanceof \Closure){
            $c = $this->repositories;
            $this->repositories = $c();
        }

        return $this->repositories;
    }
}
