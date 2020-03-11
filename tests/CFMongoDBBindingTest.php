<?php declare(strict_types=1);

namespace PHPCFEnv\CFEnv;

use PHPUnit\Framework\TestCase;

final class CFMongoDBBindingTest extends TestCase {

    public function testBinding() {
        $jsonString = file_get_contents(__DIR__.'/cf-service-mongodb.json');
        
        $service = new CFService('mongodb', json_decode($jsonString, true));
        
        $binding = new CFMongoDBBinding();
        $binding->bind($service);
        $client = $binding->getMongoDBClient();
        $this->assertIsObject($client);
    }

}