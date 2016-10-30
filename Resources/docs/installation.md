Installation
============

## Install AbcSupervisorBundle

Download the bundle using composer:

```
$ composer require "aboutcoders/supervisor-bundle:dev-master"
```

Include the bundle in the AppKernel.php class

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Abc\Bundle\SupervisorBundle\AbcSupervisorBundle(),
    );

    return $bundles;
}
```

## Install REST Bundles (Optional)

If you want to use the REST-API make sure the following additional bundles are installed and configured:

* [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)

Next Step: [Configuration](./configuration.md)