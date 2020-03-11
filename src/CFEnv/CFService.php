<?php
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