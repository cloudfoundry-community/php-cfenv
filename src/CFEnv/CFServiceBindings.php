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
 * This is a helper class providing methods to make it easier to consume
 * bound services.
 */
class CFServiceBindings {

    /**
     * @var CFEnv
     */
    private $env;

    public function __construct($applicationFile = null, $servicesFile = null) {
        $this->env = new CFEnv($applicationFile, $servicesFile);
        $this->env->load();
    }

    /**
     * @return CFEnv
     */
    public function getEnv() {
        return $this->env;
    }

    /**
     * Return the named database binding
     * If $name is not supplied, just return the default if there is one
     * Return null if no binding was found 
     * @return CFPDOBinding
     */
    public function getPDOBinding($name = null) {
        try {
            if(!empty($name)) {
                $service = $this->env->getServiceByName($name);
            } else {
                $service = $this->env->getDatabase();
            }
            $binding = new CFPDOBinding();
            $binding->bind($service);
            return $binding;
        } catch (CFServiceNotFoundException | CFServiceNotUniqueException $e) {
            return null;
        }
    }

    /**
     * @return CFRedisBinding
     * @throw CFServiceNotFoundException
     * @throw CFServiceNotUniqueException
     */
    public function getRedisBinding($name = null) {
        return $this->getServiceBinding($name, 'redis', new CFRedisBinding());
    }

    /**
     * @return CFMongoDBBinding
     * @throw CFServiceNotFoundException
     * @throw CFServiceNotUniqueException
     */
    public function getMongoDBBinding($name = null) {
        return $this->getServiceBinding($name, 'mongodb', new CFMongoDBBinding());
    }

    /**
     * @return CFNFSBinding
     * @throw CFServiceNotFoundException
     * @throw CFServiceNotUniqueException
     */
    public function getNFSBinding($name = null) {
        return $this->getServiceBinding($name, 'nfs', new CFNFSBinding());
    }

    protected function getServiceBinding($serviceName, $default, $binding) {
         try {
             if(empty($serviceName)) {
                 $service = $this->env->getServiceByName($default);
             } else {
                $service = $this->env->getServiceByName($serviceName);
             }
             $binding->bind($service);
            return $binding;
        } catch (CFServiceNotFoundException | CFServiceNotUniqueException $e) {
            return null;
        }
    }
}