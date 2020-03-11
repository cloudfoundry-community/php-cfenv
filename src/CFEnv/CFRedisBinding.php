<?php

namespace PHPCFEnv\CFEnv;
use Predis\Client;

class CFRedisBinding implements CFBinding {

    private $service;
    private $client;

    public function bind(CFService $service) {
        $this->service = $service;
        $creds = $service->getCredentials();

        $this->client = new Client([
            'scheme' => 'tcp',
            'host' => $creds->getHost(),
            'port' => $creds->getPort(),
            'password' => $creds->getPassword(),
        ]);
    }

    public function getRedisClient() {
        return $this->client;
    }
}