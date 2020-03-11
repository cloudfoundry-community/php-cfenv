<?php

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

    private $application;
    private $services = array();

    /**
     * Initialise from the environment variables
     * @return void
     */
    public function load() {
        $this->loadApplication(getenv(self::VCAP_APPLICATION));
        $this->loadServices(getenv(self::VCAP_SERVICES));
    }

    /**
     * Initialise the services from the given JSON string
     */
    public function loadServices($vcapServicesJson) {
        $vcapServicesJson = trim($vcapServicesJson);
        $this->services = array();
        if(strlen($vcapServicesJson) > 0) {
            $servicesArray = json_decode($vcapServicesJson, true);
            foreach($servicesArray as $serviceType => $serviceTypeArray) {
                foreach($serviceTypeArray as $service) {
                    $this->services[] = new CFService($serviceType, $service);
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
            $this->application = new CFApplication(json_decode($vcapApplicationJson, true));
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
     * @throw NonUniqueServiceException if there is more than one matching service
     */
    public function getServiceByName($namePattern) {
        $services = array();
        foreach($this->services as $service) {
            if(preg_match('#'.$namePattern.'#', $service->getName())) {
                $services[] = $service;
            }
        }
        if(count($services) > 1) {
            throw new CFNonUniqueServiceException("Multiple services match '$namePattern'");
        }

        if(count($services) == 0) {
            return null;
        }

        return $services[0];
    }

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
            throw new CFNonUniqueServiceException("Multiple services match '$namePattern'");
        }

        if(count($services) == 0) {
            return null;
        }

        return $services[0];
    }

    /**
     * Convenience method to return a database service
     * It can be hard to track down a database connection, so this method searches using the name
     * and checks the tags for any of the known database names also
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
            throw new CFNonUniqueServiceException("Multiple database services found");
        }

        if(count($services) == 0) {
            return null;
        }

        return $services[0];
    }
}