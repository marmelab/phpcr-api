PHPCR API
=============

PHPCR API provides an API to explore PHPCR repositories. The current implementation supports Jackalope Jackrabbit.

Installation
------------

Utilisation
-------------
```php
$repositoriesConfig = array(
    'Repository Test' => array(
        'factory' => jackalope.jackrabbit,
        'parameters' => array(
            'jackalope.jackrabbit_uri' => 'http://localhost:8080/server',
            'credentials.username' => 'admin',
            'credentials.password' => 'admin'
        )
    )
);

$loader = new \PHPCRAPI\API\RepositoryLoader($repositoriesConfig);

$repositoryTest = new \PHPCRAPI\API\Manager\RepositoryManager(
    $loader->getRepositories()->get('Repository Test')
);

$session = $repositoryTest->getSessionManager('MyWorkspace');

$rootNode = $session->getNode('/');
```

The `factory` setting is the type of PHPCR repository you want to browse. See available factories in [config/factories.yml](config/factories.yml).

See [src/PHPCRAPI/API/Manager](src/PHPCRAPI/API/Manager) to discover all available methods.

License
-------

This application is available under the MIT License, courtesy of [marmelab](http://marmelab.com).
