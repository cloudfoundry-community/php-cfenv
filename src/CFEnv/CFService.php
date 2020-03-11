<?php

namespace PHPCFEnv\CFEnv;

class CFService {

    const TAGS = "tags";
    const CREDENTIALS = "credentials";
    const VOLUME_MOUNTS = "volume_mounts";

    private $type;
    private $serviceData = array();
    private $cfCredentials;
    private $cfVolumes = array();


    public function __construct(string $serviceType, array $jsonArray) {
        $this->serviceType = $serviceType;
        $this->serviceData = $jsonArray;
        $this->cfCredentials = $this->createCredentials();
        $this->cfVolumes = $this->createVolumes();
    }

    public function createCredentials() {
        $creds = array();
        if(array_key_exists(self::CREDENTIALS, $this->serviceData)) {
            $creds = $this->serviceData[self::CREDENTIALS];
        }

        return new CFCredentials($creds);
    }

    public function createVolumes() {
        $vols = array();
        if(array_key_exists(self::VOLUME_MOUNTS, $this->serviceData)) {
            $volData = $this->serviceData[self::VOLUME_MOUNTS];
            foreach($volData as $data) {
                $vols[] = new CFVolume($data);
            }
        }
        return $vols;
    }

    public function getServiceType() {
        return $this->serviceType;
    }

    public function getMap() {
        return $this->serviceData;
    }

    public function getCredentials() {
        return $this->cfCredentials;
    }

    public function getVolumes() {
        return $this->cfVolumes;
    }

    public function getTags() {
        $tags = array();
        if(array_key_exists(self::TAGS, $this->serviceData)) {
            $tags = $this->serviceData[self::TAGS];
        }
        return $tags;
    }

    public function getLabel() {
        return $this->getString('label');
    }

    public function getPlan() {
        return $this->getString('plan');
    }

    public function getName() {

        $name = $this->getString('name');
        if(is_null($name)) {
            $name = $this->getString('binding_name');
        }
        if(is_null($name)) {
            $name = $this->getString('instance_name');
        }
        return $name;
    }

    public function getString($key) {
        if(array_key_exists($key, $this->serviceData) && is_string($this->serviceData[$key])) {
            return $this->serviceData[$key];
        }
        return null;
    }

}