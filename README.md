# php-cfenv
PHP implementation of helper classes like java-cfenv to make it easier for PHP applications to bind to services in Cloud Foundry

## Credits
I took inspiration from cf-helper-php and I used example configurations for testing taken from java-cfenv:

* cf-helper-php - https://github.com/cloudfoundry-community/cf-helper-php
* java-cfenv - https://github.com/pivotal-cf/java-cfenv

## Demo App
There's a simple demo app showing binding to MongoDB here:

https://github.com/dmcintyre-pivotal/php-cfenv-demo

## Usage
If you use Composer then you can simply add the dependency to your composer.json
```php
{
	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/dmcintyre-pivotal/php-cfenv"
		}
	],
	"require": {
		"dmcintyre-pivotal/php-cfenv": "dev-master"
	}
}
```
## Getting Bindings
### Defaults
If there is only one binding of a given type (i.e. only one relational database)
then getting hold of it is easy.
```PHP
<?php

$bindings = new CFServiceBindings();
$databaseBinding = $bindings->getPDOBinding();
$databaseConnection = $databaseBinding->getPDOConnection();

// similarly for MongoDB
$mongodbBinding = $bindings->getMongoDBBinding();
$mongodbClient = $mongodbBinding->getMongoDBClient();
// You'll need to start by connecting to your database
$databaseName = $mongodbBinding->getDatabaseName();
$database = $mongodbClient->selectDatabase($path);

// Redis
$redisBinding = $bindings->getRedisBinding();
$redisClient = $redisBinding->getRedisClient();

// NFS
$nfsBinding = $bindings->getNFSBinding();
$volumes = $nfsBinding->getVolumes();
```
### Named bindings
If there are more than one binding of a given type, you can pass the binding name:
```
<?php

$bindings = new CFServiceBindings();
$databaseBinding = $bindings->getPDOBinding("catalog_db");
$databaseConnection = $databaseBinding->getPDOConnection();
...
...
```


## Built-in Bindings
The package provides three bindings
1. MongoDB
2. Redis
3. PDO - This binding understands MySql, Oracle, Postgresql, MariaDB, and Sqlite databases

## CFEnv
If the helper methods supplied by `CFServiceBindings` are not sufficient for your needs, you can 
access services using the underlying `CFEnv` object's methods

* getServiceByName($regexPattern)
* getServiceByTags(array $tags)
* getDatabase()

The `CFEnv` methods `getServiceByName`, `getServiceByTags` and `getDatabase` can throw exceptions

 * CFServiceNotFoundException - if no service matching the criteria could be found
 * CFServiceNotUniqueException - if more than one service matching the criteria was found

 Additionally the `CFEnv` method `load` can throw

 * CFFileUnreadableException - if a defaults file could not be read (see below)

## Supplying defaults
In local dev environments it may be easier to supply settings in a file rather than through environment variables.
This is accomplished by providing a path to a file containing JSON formatted settings corresponding to VCAP_APPLICATION and VCAP_SERVICES:

```
$bindings = new CFServiceBindings("/usr/local/etc/myapp/application.json", "/usr/local/etc/myapp/services.json");

```

Either file may be omitted (set to null)
```
$bindings = new CFServiceBindings(null, "/usr/local/etc/myapp/services.json");
```

If a file is specified and cannot be read, a `CFFileUnreadableException` will be thrown.

The files will only be parsed if the VCAP_APPLICATION environment variable is not set. If this variable is set then it is assumed that the app is running in CloudFoundry.