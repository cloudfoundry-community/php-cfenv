<?php declare(strict_types=1);

namespace PHPCFEnv\CFEnv;

use PHPUnit\Framework\TestCase;

final class CFRedisBindingTest extends TestCase {

    public function testBinding() {
        $jsonString = file_get_contents(__DIR__.'/cf-service-redis.json');
        
        $service = new CFService('mongodb', json_decode($jsonString, true));
        
        $binding = new CFRedisBinding();
        $binding->bind($service);
        $client = $binding->getRedisClient();
        $this->assertIsObject($client);
    }

}