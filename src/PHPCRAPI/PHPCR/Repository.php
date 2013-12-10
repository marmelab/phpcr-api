<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\PHPCR;

/**
 * Repository is a wrapper for a PHPCR Repository
 * Helps us to add metadata to it and provides more control possibilities
 *
 * @api
 */
class Repository implements CollectionItemInterface
{
    /**
     * PHPCR Repository the repository refers to
     *
     * @var \PHPCR\RepositoryInterface $repository The PHPCR Repository
     */
    private $repository;

    /**
     * Factory used to instantiate the PHPCR Repository
     *
     * @var Factory $factory The factory class
     */
    private $factory;

    /**
     * Repository constructor
     *
     * @param string  $name       Repository's name
     * @param Factory $factory    Factory
     * @param array   $parameters Parameters of the repository
     *
     * @api
     */
    public function __construct($name, Factory $factory, array $parameters = array())
    {
        $this->name = $name;
        $this->factory = $factory;
        $this->parameters = $parameters;

        $this->repository = function () use ($factory, $parameters) {
            $factoryClass = $factory->getClass();
            $factory = new $factoryClass();

            return $factory->getRepository($parameters);
        };
    }

    /**
     * Return the factory
     *
     * @return Factory Factory of the repository
     *
     * @api
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Return the parameters
     *
     * @return string Parameters of the repository
     *
     * @api
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Return name
     *
     * @return string Name of the repository
     *
     * @api
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the wrapped PHPCR Repository
     *
     * @return \PHPCR\RepositoryInterface The wrapped repository
     */
    private function getRepository()
    {
        $c = $this->repository;
        if ($c instanceof \Closure) {
            $this->repository = $c();
        }

        return $this->repository;
    }

    /**
     * Call bridge with the wrapped PHPCR Repository
     *
     * @param string $funcName Function's name
     * @param array  $args     Function's arguments
     */
    public function __call($funcName, $args)
    {
        return call_user_func_array(array($this->getRepository(), $funcName), $args);
    }
}
