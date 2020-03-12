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

class CFNFSBinding extends CFBinding {

    private $service;

    /**
     * Bind to the given service instance.
     * Throw an exception if the service instance is not appropriate
     * @throw CFUnsupportedBindingException 
     */
    public function bind(CFService $service) {
        if(!$service->hasTag('nfs')) {
            throw new CFUnsupportedBindingException("CFNFSBinding cannot be bound to the service '".$service->getName());
        }
        $this->service = $service;
    }

    public function getVolumes() {
        return $this->service->getVolumes();
    }
}