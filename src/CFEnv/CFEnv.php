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

/**
 * Helper class to simplify consuming services bound to an application running in Cloud Foundry.
 * 
 * When an app is running in CF there will be two environment variables set
 * 
 * VCAP_APPLICATION
 * 
 * VCAP_SERVICES - contains a JSON array of service bindings 
 */
class CFEnv {

    const VCAP_APPLICATION = 'VCAP_APPLICATION';
    const VCAP_SERVICES = 'VCAP_SERVICES';

    private $applicationFile;
    private $servicesFile;
    private $loaded = false;

    private $application;
    private $services = array();

    public function __construct($applicationFile = null, $servicesFile = null) {
        $this->applicationFile = $applicationFile;
        $this->servicesFile = $servicesFile;
    }

    /**
     * Initialise from the environment variables
     * @return void
     */
    public function load() {
        if(!$this->loaded) {
            if($this->isInCloudFoundry()) {
                $this->loadApplication(getenv(self::VCAP_APPLICATION));
                $this->loadServices(getenv(self::VCAP_SERVICES));
            } else {
                if(isset($this->applicationFile) && !empty($this->applicationFile)) {
                    if(is_readable($this->applicationFile)) {
                        $this->loadApplication(file_get_contents($this->applicationFile));
                    } else {
                        throw new CFFileUnreadableException("Cannot read application configuration from ".$this->applicationFile);
                    }
                }
                if(isset($this->servicesFile) && !empty($this->servicesFile)) {
                    if(is_readable($this->servicesFile)) {
                        $this->loadServices(file_get_contents($this->servicesFile));
                    } else {
                        throw new CFFileUnreadableException("Cannot read services configuration from ".$this->servicesFile);
                    }
                }
            }
            $this->loaded = true;
        }
    }

    public function isInCloudFoundry() {
        return !empty(getenv(self::VCAP_APPLICATION));
    }

    /**
     * Initialise the services from the given JSON string
     */
    public function loadServices($vcapServicesJson) {
        $vcapServicesJson = trim($vcapServicesJson);
        $this->services = array();
        if(strlen($vcapServicesJson) > 0) {
            $servicesArray = json_decode($vcapServicesJson, true);
            if(is_array($servicesArray)) {
                foreach($servicesArray as $serviceType => $serviceTypeArray) {
                    foreach($serviceTypeArray as $service) {
                        $this->services[] = new CFService($serviceType, $service);
                    }
                }
            }
        }
    }

    /**
     * Initialise the application from the given JSON string
     */
    public function loadApplication($vcapApplicationJson) {
        $vcapApplicationJson = trim($vcapApplicationJson);
        if(strlen($vcapApplicationJson) > 0) {
            $appData = json_decode($vcapApplicationJson, true);
            if(is_array($appData)) {
                $this->application = new CFApplication($appData);
            }
        }
    }

    /**
     * @return CFApplication
     */
    public function getApplication() {
        return $this->application;
    }

    /**
     * @return array of CFService
     */
    public function getServices() {
        return $this->services;
    }

    /**
     * Return the service binding which matches the name pattern
     * @param string $namePattern A regular expression to match against name
     * @return CFService
     * @throw CFNonUniqueServiceException if there is more than one matching service
     * @throw CFServiceNotFoundException if there is no matching service
     */
    public function getServiceByName($namePattern) {
        $services = array();
        foreach($this->services as $service) {
            if(preg_match('#'.$namePattern.'#', $service->getName())) {
                $services[] = $service;
            }
        }
        if(count($services) > 1) {
            throw new CFServiceNotUniqueException("Multiple services match '$namePattern'");
        }

        if(count($services) == 0) {
            throw new CFServiceNotFoundException('No service matches pattern '.$namePattern);
        }

        return $services[0];
    }

    /**
     * Return the service which has any of the given tags
     * @throw CFNonUniqueServiceException if more than one service matches
     * @throw CFServiceNotFoundException if there is no matching service
     */
    public function getServiceByTags(array $tags) {
        $services = array();
        foreach($this->services as $service) {
            foreach($tags as $tag) {
                if(in_array($tag, $service->getTags())) {
                    $services[] = $service;
                }
            }
        }
        if(count($services) > 1) {
            throw new CFServiceNotUniqueException('Multiple services match tags '.implode("|", $tags));
        }

        if(count($services) == 0) {
            throw new CFServiceNotFoundException('No service matches tags '.implode("|", $tags));
        }

        return $services[0];
    }

    /**
     * Convenience method to return a database service
     * It can be hard to track down a database connection, so this method searches using the name
     * and checks the tags for any of the known database names also
     * @throw CFNonUniqueServiceException if there is more than one database service
     * @throw CFServiceNotFoundException if there is no matching service
     */
    public function getDatabase() {
        $services = array();
        foreach($this->services as $service) {
            $found = false;
            $tags = $service->getTags();
            foreach($tags as $tag) {
                if(preg_match('#('.CFPDOBinding::SUPPORTED_DATABASES.')#', $tag)) {
                    $found = true;
                }
            }
            if($found) {
                $services[] = $service;
            }
        }

        if(count($services) > 1) {
            throw new CFServiceNotUniqueException("Multiple database services found");
        }

        if(count($services) == 0) {
            throw new CFServiceNotFoundException("No database service found");
        }

        return $services[0];
    }
}