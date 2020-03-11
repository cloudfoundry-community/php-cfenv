php-cfenv
=========
A php library to help bind services to applications.

Usage:

$env = new CFEnv();
$env->load();
$service = $env->getServiceByNameOrType("mongodb");
$binding = new MongoDBBinding($service);
$client = $binding->getClient();


etc.

This allows you to supply your own binding class for other types of service.
