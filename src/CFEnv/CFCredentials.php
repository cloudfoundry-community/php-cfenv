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

class CFCredentials {

    private $credentialsData = array();
    private $uriInfo = null;
 
    public function __construct(array $jsonArray) {
        $this->credentialsData = $jsonArray;
        $this->createUriInfo();
    }

    public function getMap() {
        return $this->credentialsData;
    }

    public function getHost() {
        $host = $this->getString(array("host", "hostname"));
        if(!is_null($host)) {
            return $host;
        }

        if(array_key_exists("host", $this->uriInfo)) {
            return $$this->uriInfo['host'];
        }

        return null;
    }

    public function getPort() {
        $port = $this->getString(array("port"));
        if(!is_null($port)) {
            return $port;
        }

        if(array_key_exists("port", $this->uriInfo)) {
            return $this->uriInfo['port'];
        }

        return null;
    }

    public function getName() {
        return $this->getString(array("name"));
    }

    public function getUsername() {
        $username = $this->getString(array("username", "user"));
        if(!is_null($username)) {
            return $username;
        }

        if(array_key_exists("user", $this->uriInfo)) {
            return $this->uriInfo['user'];
        }

        return null;
    }


   public function getPassword() {
        $password = $this->getString(array("password"));
        if(!is_null($password)) {
            return $password;
        }

        if(array_key_exists("pass", $this->uriInfo)) {
            return $this->uriInfo['pass'];
        }

        return null;
    }

    /**
     * Returns the path part of the URI with any leading '/' removed
     */
    public function getPath() {
        if(isset($this->uriInfo['path']) && !empty($this->uriInfo['path'])) {
            return str_replace('/','',$this->uriInfo['path']);
        }
        return null;
    }

    public function getUri() {
        return $this->getString(array("uri", "url"));
    }

    public function getUriInfo() {
        return $this->uriInfo;
    }

    private function createUriInfo() {
        if(empty($this->uriInfo)) {
            $this->uriInfo = parse_url($this->getString(array("uri","url")));
        }

        return $this->uriInfo;
    }

    private function getString(array $keys) {
        if(!is_null($this->credentialsData)) {
            foreach($keys as $key) {
                if(array_key_exists($key, $this->credentialsData)) {
                    return $this->credentialsData[$key];
                }
            }
        }

        return null;
    }
}