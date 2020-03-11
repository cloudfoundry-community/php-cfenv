<?php declare(strict_types=1);

namespace PHPCFEnv\CFEnv;

use PHPUnit\Framework\TestCase;

final class CFServiceTest extends TestCase {

    public function testMySQLService() {
        $jsonString = file_get_contents(__DIR__.'/cf-service-mysql.json');
        
        $service = new CFService('p-mysql', json_decode($jsonString, true));

        $this->assertEquals('p-mysql' , $service->getServiceType());
        $this->assertEquals("p-mysql" , $service->getLabel());
        $this->assertEquals("100mb" , $service->getPlan());
        $this->assertEquals("mysql" , $service->getName());
        $this->assertIsObject($service->getCredentials());
        $this->assertIsArray($service->getVolumes());
        $this->assertEquals(array("mysql", "relational"), $service->getTags());
    }

    public function testNFSService() {
        $jsonString = file_get_contents(__DIR__.'/cf-service-nfs.json');
        
        $service = new CFService('nfs', json_decode($jsonString, true));

        $this->assertEquals('nfs' , $service->getServiceType());
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
        
        $service = new CFService('mongodb', json_decode($jsonString, true));
        $creds = $service->getCredentials();
        $this->assertEquals("mongodb://CloudFoundry_topSecret:s3cr3t@bigbox.mongodbsaas.tst:11128/CloudFoundry_topSecret",
            $creds->getUri());
        $this->assertEquals("CloudFoundry_topSecret", $creds->getUsername());
        $this->assertEquals("s3cr3t", $creds->getPassword());
    }
}