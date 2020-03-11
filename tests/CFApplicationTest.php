<?php declare(strict_types=1);

namespace PHPCFEnv\CFEnv;

use PHPUnit\Framework\TestCase;

final class CFApplicationTest extends TestCase {

    public function testGetters() {
        $jsonString = file_get_contents(__DIR__.'/vcap-application.json');
        $app = new CFApplication(json_decode($jsonString, true));
        $this->assertIsObject($app);
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
}