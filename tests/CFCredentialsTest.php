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

final class CFCredentialsTest extends TestCase {

    public function testCredentials() {
        $jsonString = file_get_contents(__DIR__.'/cf-credentials.json');
        
        $creds = new CFCredentials(json_decode($jsonString, true));

        $this->assertEquals("10.0.4.35", $creds->getHost());
        $this->assertEquals(3306, $creds->getPort());
        $this->assertEquals("mysql_name", $creds->getName());
        $this->assertEquals("mysql_username", $creds->getUsername());
        $this->assertEquals("mysql_password", $creds->getPassword());
    }
}