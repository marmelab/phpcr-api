<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\PHPCR;

class Factory implements CollectionItemInterface
{
    private $name;

    private $class;

    private $parameters = array();

    private $supportedOperations = array();

    public function __construct($name, $class, $parameters, $supportedOperations)
    {
        $this->name = $name;
        $this->class = $class;
        $this->parameters = $parameters;
        $this->supportedOperations = $supportedOperations;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function instanciate()
    {
        return new $this->class();
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getSupportedOperations()
    {
        return $this->supportedOperations;
    }
}
