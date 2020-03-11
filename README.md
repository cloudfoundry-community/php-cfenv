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
* You'll need to start by connecting to your database, and it's name is given
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

