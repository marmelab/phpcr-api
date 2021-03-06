<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\API;

use PHPCRAPI\PHPCR\Loader\FactoryConfigurationLoader;
use PHPCRAPI\PHPCR\Loader\RepositoryConfigurationLoader;
use Symfony\Component\Yaml\Parser;

class RepositoryLoader
{
    private $repositories;

    public function __construct(array $repositoriesConfiguration = array())
    {
        $this->repositories = function () use ($repositoriesConfiguration) {
            $parser = new Parser();
            $factoriesConfiguration = $parser->parse(file_get_contents(__DIR__.'/../../../config/factories.yml'));
            $factoryLoader = new FactoryConfigurationLoader($factoriesConfiguration['phpcr_factories']);
            $repositoryLoader = new RepositoryConfigurationLoader($repositoriesConfiguration, $factoryLoader);

            return $repositoryLoader->getRepositories();
        };
    }

    public function getRepositories()
    {
        if ($this->repositories instanceof \Closure) {
            $c = $this->repositories;
            $this->repositories = $c();
        }

        return $this->repositories;
    }
}
