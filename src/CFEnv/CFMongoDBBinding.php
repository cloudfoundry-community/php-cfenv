<?php

namespace PHPCFEnv\CFEnv;
use MongoDB\Client;

class CFMongoDBBinding implements CFBinding {

    private $service;
    private $client;

    public function bind(CFService $service) {
        $this->service = $service;
        $this->client = new Client($service->getCredentials()->getUri());
    }

    public function getMongoDBClient() {
        return $this->client;
    }
}