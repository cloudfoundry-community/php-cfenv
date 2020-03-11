<?php

namespace PHPCFEnv\CFEnv;



class CFCredentials {

    private $credentialsData = array();
    private $uriInfo = array();
 
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