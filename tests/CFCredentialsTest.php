<?php declare(strict_types=1);

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