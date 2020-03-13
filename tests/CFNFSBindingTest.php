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

final class CFNFSBindingTest extends TestCase {

    public function testBinding() {
        $jsonString = file_get_contents(__DIR__.'/cf-service-nfs.json');
        
        $service = new CFService(json_decode($jsonString, true));
        
        $binding = new CFNFSBinding();
        $binding->bind($service);
        $volumes = $binding->getVolumes();
        $this->assertEquals(1, count($volumes));
        $this->assertEquals("/var/vcap/data/78525ee7-196c-4ed4-8ac6-857d15334631", $volumes[0]->getPath());
    }

    function testGetService() {
        $jsonString = file_get_contents(__DIR__.'/cf-service-nfs.json');
        
        $service = new CFService(json_decode($jsonString, true));
        
        $binding = new CFNFSBinding();
        $binding->bind($service);
        $service = $binding->getService();
        $this->assertInstanceOf(CFService::class, $service);
    }
}