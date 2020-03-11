<?php declare(strict_types=1);

namespace PHPCFEnv\CFEnv;

use PHPUnit\Framework\TestCase;

final class CFEnvTest extends TestCase {


    public function testLoadNull() {
        $env = new CFEnv();
        $env->loadServices('');
        $env->loadApplication('');

        $this->assertCount(0, $env->getServices());
        $this->assertNull($env->getApplication());
    }

    public function testSingleServiceInstances() {
        $jsonString = file_get_contents(__DIR__.'/vcap-services.json');
        $env = new CFEnv();
        $env->loadServices($jsonString);

        $services = $env->getServices();
        $this->assertEquals(3, count($services));
    }

    public function testGetByName() {
        $jsonString = file_get_contents(__DIR__.'/vcap-services.json');
        $env = new CFEnv();
        $env->loadServices($jsonString);

        $service = $env->getServiceByName("mysql");
        $this->assertIsObject($service);
    }

    public function testGetSingleDatabase() {
        $jsonString = file_get_contents(__DIR__.'/vcap-services.json');
        $env = new CFEnv();
        $env->loadServices($jsonString);
        $service = $env->getDatabase();
        $this->assertIsObject($service);
        $this->assertEquals("mysql", $service->getName());
    }

    public function testMultipleServiceInstances() {
        $jsonString = file_get_contents(__DIR__.'/vcap-services-multiple-mysql.json');
        $env = new CFEnv();
        $env->loadServices($jsonString);
        $this->expectException(CFNonUniqueServiceException::class);
        $service = $env->getDatabase();
    }
}