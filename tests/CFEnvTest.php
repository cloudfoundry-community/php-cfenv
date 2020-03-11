<?php declare(strict_types=1);
/*
 * Copyright 2019 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
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

    public function testMultipleDatabaseInstances() {
        $jsonString = file_get_contents(__DIR__.'/vcap-services-multiple-mysql.json');
        $env = new CFEnv();
        $env->loadServices($jsonString);
        $this->expectException(CFNonUniqueServiceException::class);
        $service = $env->getDatabase();
    }

    public function testMultipleNamedInstances() {
        $jsonString = file_get_contents(__DIR__.'/vcap-services-multiple-mysql.json');
        $env = new CFEnv();
        $env->loadServices($jsonString);
        $this->expectException(CFNonUniqueServiceException::class);
        $service = $env->getServiceByName("mysql");
    }
    
    public function testMultipleTaggedInstances() {
        $jsonString = file_get_contents(__DIR__.'/vcap-services-multiple-mysql.json');
        $env = new CFEnv();
        $env->loadServices($jsonString);
        $this->expectException(CFNonUniqueServiceException::class);
        $service = $env->getServiceByTags(array("relational","mysql"));
    }
}