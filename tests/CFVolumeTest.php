<?php declare(strict_types=1);

namespace PHPCFEnv\CFEnv;

use PHPUnit\Framework\TestCase;

final class CFVolumeTest extends TestCase {

    public function testVolume() {
        $jsonString = file_get_contents(__DIR__.'/cf-service-nfs.json');
        
        $service = new CFService('nfs', json_decode($jsonString, true));
        $vols = $service->getVolumes();
        $this->assertIsArray($vols);
        $this->assertEquals(1, count($vols));
        $vol = $vols[0];

        $this->assertEquals("/var/vcap/data/78525ee7-196c-4ed4-8ac6-857d15334631", $vol->getPath());
        $this->assertEquals("rw", $vol->getMode());

    }
}