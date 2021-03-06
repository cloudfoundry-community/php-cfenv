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

    public function getEnv($servicesFile = null, $applicationFile = null) {
        if(!empty($applicationFile)) {
            $applicationFile = __DIR__.'/'.$applicationFile;
        } 
        
        if(!empty($servicesFile)) {
            $servicesFile = __DIR__.'/'.$servicesFile;
        } 
        
         $env = new CFEnv($applicationFile, $servicesFile);
         $env->load();
         return $env;
    }

    public function testApplication() {
        $env = $this->getEnv(null, 'vcap-application.json');

        $this->assertInstanceOf(CFApplication::class, $env->getApplication());
    }

    public function testIsInCloudFoundry() {

        $json = json_encode(file_get_contents(__DIR__.'/vcap-application.json'));
        $json = str_replace('\\n','',$json);
        $json = str_replace('\\"','"', $json);
        // Strip the leading and trailing '"' so it can be passed to putenv
        $json = substr($json, 1, strlen($json) - 2);
 
        putenv('VCAP_APPLICATION='.$json);
        $read = getenv('VCAP_APPLICATION');
        $env = $this->getEnv();
        $this->assertInstanceOf(CFApplication::class, $env->getApplication());
        putenv('VCAP_APPLICATION');
    }

    public function testSingleServiceInstances() {
        $env = $this->getEnv('vcap-services.json');
        
        $services = $env->getServices();
        $this->assertEquals(4, count($services));
    }

    public function testGetByName() {
        $env = $this->getEnv('vcap-services.json');
 
        $service = $env->getServiceByName("mysql");
        $this->assertInstanceOf(CFService::class, $service);
    }

    public function testGetSingleDatabase() {
        $env = $this->getEnv('vcap-services.json');
 
        $service = $env->getDatabase();
        $this->assertInstanceOf(CFService::class, $service);
        $this->assertEquals("mysql", $service->getName());
    }

    public function testMultipleDatabaseInstances() {
        $env = $this->getEnv('vcap-services-multiple-mysql.json');
        
        $this->expectException(CFServiceNotUniqueException::class);
        $service = $env->getDatabase();
    }

    public function testMultipleNamedInstances() {
        $env = $this->getEnv('vcap-services-multiple-mysql.json');
        
        $this->expectException(CFServiceNotUniqueException::class);
        $service = $env->getServiceByName("mysql");
    }

    public function testMultipleTaggedInstances() {
        $env = $this->getEnv('vcap-services-multiple-mysql.json');
        
        $this->expectException(CFServiceNotUniqueException::class);
        $service = $env->getServiceByTags(array("relational","mysql"));
    }

    public function testNoNameMatch() {
        $env = $this->getEnv('vcap-services-multiple-mysql.json');
        
        $this->expectException(CFServiceNotFoundException::class);
        $service = $env->getServiceByName("I'm not here");
    }

    public function testNoTagsMatch() {
        $env = $this->getEnv('vcap-services-multiple-mysql.json');
        
        $this->expectException(CFServiceNotFoundException::class);
        $service = $env->getServiceByTags(array("missing","tags"));
    }

    public function testNoDatabase() {
        $env = $this->getEnv('vcap-services-multiple-redis.json');
        
        $this->expectException(CFServiceNotFoundException::class);
        $service = $env->getDatabase();
    }
}