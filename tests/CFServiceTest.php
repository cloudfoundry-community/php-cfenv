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

final class CFServiceTest extends TestCase {

    public function testMySQLService() {
        $jsonString = file_get_contents(__DIR__.'/cf-service-mysql.json');
        
        $service = new CFService(json_decode($jsonString, true));

        $this->assertTrue($service->hasTag('mysql'));
        $this->assertEquals("p-mysql" , $service->getLabel());
        $this->assertEquals("100mb" , $service->getPlan());
        $this->assertEquals("mysql" , $service->getName());
        $this->assertIsObject($service->getCredentials());
        $this->assertIsArray($service->getVolumes());
        $this->assertEquals(array("mysql", "relational"), $service->getTags());
    }

    public function testNFSService() {
        $jsonString = file_get_contents(__DIR__.'/cf-service-nfs.json');
        
        $service = new CFService(json_decode($jsonString, true));

        $this->assertTrue($service->hasTag('nfs'));
        $this->assertEquals("nfs" , $service->getLabel());
        $this->assertEquals("Existing" , $service->getPlan());
        $this->assertEquals("nfs1" , $service->getName());
        $this->assertIsObject($service->getCredentials());
        $vols = $service->getVolumes();
        $this->assertIsArray($vols);
        $this->assertEquals(1, count($vols));
        $this->assertEquals(array("nfs"), $service->getTags());
    }


    public function testMongoDBCredentials() {
        $jsonString = file_get_contents(__DIR__.'/cf-service-mongodb.json');
        
        $service = new CFService(json_decode($jsonString, true));
        $creds = $service->getCredentials();
        $this->assertEquals("mongodb://CloudFoundry_topSecret:s3cr3t@bigbox.mongodbsaas.tst:11128/CloudFoundry_topSecret",
            $creds->getUri());
        $this->assertEquals("CloudFoundry_topSecret", $creds->getUsername());
        $this->assertEquals("s3cr3t", $creds->getPassword());
    }
}