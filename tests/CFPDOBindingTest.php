<?php declare(strict_types=1);

namespace PHPCFEnv\CFEnv;

use PHPUnit\Framework\TestCase;

final class CFPDOBindingTest extends TestCase {

    public function testBinding() {
        $jsonString = file_get_contents(__DIR__.'/cf-service-mysql.json');
        
        $service = new CFService('mysql', json_decode($jsonString, true));
        
        $binding = new CFPDOBinding();
        $binding->bind($service);
        $dsn = $binding->getDSN();
        $this->assertEquals('mysql:host=10.0.4.35;port=3306;dbname=cf_2e23d10a_8738_8c3c_66cf_13e44422698c', $dsn);
    }

}