# php-cfenv
PHP implementation of helper classes like java-cfenv to make it easier for PHP applications to bind to services in Cloud Foundry

## Credits
I took inspiration from cf-helper-php and I used example configurations for testing taken from java-cfenv:

* cf-helper-php - https://github.com/cloudfoundry-community/cf-helper-php
* java-cfenv - https://github.com/pivotal-cf/java-cfenv


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
Here's an example of getting a connection to a MongoDB database
```PHP
<?php
namespace PHPCFEnv\CFEnv;
require_once __DIR__.'/../lib/vendor/autoload.php';

/**
* Construct a CFEnv object which understands how to load configuration
* from the VCAP_APPLICATION and VCAP_SERVICES environment variables
**/
$env = new CFEnv();
$env->load();

/**
* Get the service details for MongoDB
**/
$service = $env->getServiceByName("mongodb");
/**
* Bind to the service
**/
$binding = new CFMongoDBBinding();
$binding->bind($service);
/** 
* And get a MongoDB Client object configured to connect to the database
**/
$client = $binding->getMongoDBClient();

/**
* You'll need to start by connecting to your database, and its name is given
* by the 'path' component of the connection URI
**/
$creds = $service->getCredentials();
$path = $creds->getPath();

$database = $client->selectDatabase($path);
```
## Built-in Bindings
The package provides three bindings
1. MongoDB
2. Redis
3. PDO - This binding understands MySql, Oracle, Postgresql, MariaDB, and Sqlite databases

## Exceptions
The `CFEnv` methods `getServiceByName`, `getServiceByTags` and `getDatabase` can throw exceptions

 * CFServiceNotFoundException - if no service matching the criteria could be found
 * CFServiceNotUniqueException - if more than one service matching the criteria was found

 Additionally the CFEnv method `load` can throw

 * CFFileUnreadableException - if a defaults file could not be read (see below)

## Supplying defaults
In local dev envrionments it may be easier to supply settings in a file rather than through environment variables.
This is accomplished by providing a path to a file containing JSON formatted settings corresponding to VCAP_APPLICATION and VCAP_SERVICES:

```
$env = new CFEnv("/usr/local/etc/myapp/application.json", "/usr/local/etc/myapp/services.json");
$env->load();
```

Either file may be omitted (set to null)
```
$env = new CFEnv(null, "/usr/local/etc/myapp/services.json");
$env->load();
```

If a file is specified and cannot be read, a CFFileUnreadableException will be thrown when CFEnv::load() is called.

The files will only be parsed if the VCAP_APPLICATION environment variable is not set. If this variable is set then it is assumed that the app is running in CloudFoundry.