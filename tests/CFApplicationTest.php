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

final class CFApplicationTest extends TestCase {

    public function testGetters() {
        $jsonString = file_get_contents(__DIR__.'/vcap-application.json');
        $app = new CFApplication(json_decode($jsonString, true));
        $this->assertEquals("06450c72-4669-4dc6-8096-45f9777db68a",$app->getSpaceId());
        $this->assertEquals("my-space",$app->getSpaceName());
        $this->assertEquals("fe98dc76ba549876543210abcd1234",$app->getInstanceId());
        $this->assertEquals(0,$app->getInstanceIndex());
        $this->assertEquals("0.0.0.0",$app->getHost());
        $this->assertEquals(61857,$app->getPort());
        $this->assertEquals("https://api.example.com",$app->getCfApi());
        $this->assertEquals("fa05c1a9-0fc1-4fbd-bae1-139850dec7a3",$app->getApplicationId());
        $this->assertEquals("ab12cd34-5678-abcd-0123-abcdef987654",$app->getApplicationVersion());
        $this->assertEquals("styx-james",$app->getApplicationName());
        $this->assertEquals(array("my-app.example.com"),$app->getApplicationUris());
        $this->assertEquals("ab12cd34-5678-abcd-0123-abcdef987654",$app->getVersion());
        $this->assertEquals("my-app",$app->getName());
        $this->assertEquals(array("my-app.example.com"),$app->getUris());
    }

    public function testLimits() {
        $jsonString = file_get_contents(__DIR__.'/vcap-application.json');
        $app = new CFApplication(json_decode($jsonString, true));
        $limits = $app->getLimits();
        $this->assertEquals(512, $limits->getMem());
        $this->assertEquals(1024, $limits->getDisk());
        $this->assertEquals(16384, $limits->getFds());

    }
}