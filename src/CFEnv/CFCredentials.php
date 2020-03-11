<?php

namespace PHPCFEnv\CFEnv;



class CFCredentials {

    private $credentialsData = array();
    private $uriInfo = array();
 
    public function __construct(array $jsonArray) {
        $this->credentialsData = $jsonArray;
    }

    public function getMap() {
        return $this->credentialsData;
    }

    public function getHost() {
        $host = $this->getString(array("host", "hostname"));
        if(!is_null($host)) {
            return $host;
        }

        $info = $this->createOrGetUriInfo();
        if(array_key_exists("host", $info)) {
            return $info['host'];
        }

        return null;
    }

    public function getPort() {
        $port = $this->getString(array("port"));
        if(!is_null($port)) {
            return $port;
        }

        $info = $this->createOrGetUriInfo();
        if(array_key_exists("port", $info)) {
            return $info['port'];
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

        $info = $this->createOrGetUriInfo();
        if(array_key_exists("user", $info)) {
            return $info['user'];
        }

        return null;
    }


   public function getPassword() {
        $password = $this->getString(array("password"));
        if(!is_null($password)) {
            return $password;
        }

        $info = $this->createOrGetUriInfo();
        if(array_key_exists("pass", $info)) {
            return $info['pass'];
        }

        return null;
    }

    public function getUri() {
        return $this->getString(array("uri", "url"));
    }

    public function getUriInfo() {
        return $this->createOrGetUriInfo();
    }

    private function createOrGetUriInfo() {
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