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

final class CFServiceBindingsTest extends TestCase {

    public function getBindings() {
        return new CFServiceBindings(__DIR__.'/vcap-application.json', __DIR__.'/vcap-services.json');
    }

    public function testMongoDBBinding() {
        $binding = $this->getBindings()->getMongoDBBinding();
        $this->assertInstanceOf(CFMongoDBBinding::class, $binding);
    }

    public function testPDOBinding() {
        $binding = $this->getBindings()->getPDOBinding();
        $this->assertInstanceOf(CFPDOBinding::class, $binding);
    }

    public function testRedisBinding() {
        $binding = $this->getBindings()->getRedisBinding();
        $this->assertInstanceOf(CFRedisBinding::class, $binding);
    }

    public function testNFSBinding() {
        $binding = $this->getBindings()->getNFSBinding();
        $this->assertInstanceOf(CFNFSBinding::class, $binding);
    }

    public function testNoDatabase() {
        $bindings = $this->getBindings();
        $binding = $bindings->getPDOBinding("I don't exist");
        $this->assertNull($binding);
    }

    public function testNoRedis() {
        $bindings = $this->getBindings();
        $binding = $bindings->getRedisBinding("I don't exist");
        $this->assertNull($binding);
    }

    public function testNoMongoDB() {
        $bindings = $this->getBindings();
        $binding = $bindings->getMongoDBBinding("I don't exist");
        $this->assertNull($binding);
    }

    public function testNoNFS() {
        $bindings = $this->getBindings();
        $binding = $bindings->getNFSBinding("I don't exist");
        $this->assertNull($binding);
    }
}