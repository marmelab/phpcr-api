PHPCR API
=============

PHPCR API provides an API to explore PHPCR repositories. The current implementation supports Jackalope Jackrabbit and Doctrine DBAL.

Installation
------------

The recommended way to install `phpcr-api` is through Composer. Just create a
``composer.json`` file, and run the ``composer install`` command to
install it:

```json
{
    "require": {
        "marmelab/phpcr-api": "dev-master"
    }
}
```
Usage
-----

```php
$repositoriesConfig = array(
    'Repository Test' => array(
        'factory' => 'jackalope.jackrabbit',
        'parameters' => array(
            'jackalope.jackrabbit_uri' => 'http://localhost:8080/server',
            'credentials.username' => 'admin',
            'credentials.password' => 'admin'
        )
    ),
    'Repository Test2' => array(
        'factory' => 'jackalope.doctrine-dbal',
        'parameters' => array(
            'jackalope.doctrine_dbal_connection' => $dbalConnectionInstance,
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
