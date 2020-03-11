<?php

namespace PHPCFEnv\CFEnv;

use \PDO;

class CFPDOBinding implements CFBinding {

    const SUPPORTED_DATABASES = "postgres|pgsql|mariadb|mysql|oracle|oci|sqlite";
    const DBTYPE_PG = "(postgres|pgsql)";
    const DBTYPE_MYSQL = "(mariadb|mysql)";
    const DBTYPE_ORACLE = "(oracle|oci)";
    const DBTYPE_SQLITE = "sqlite";
    const DSN_PATTERN = "%s:host=%s;dbname=%s";
    const DSN_PATTERN_WITH_PORT = "%s:host=%s;port=%s;dbname=%s";
    

    private $service;
    private $connection;
    private $dsn;

    public function bind(CFService $service) {
        $this->service = $service;
        $this->dsn = $this->constructDSN();
    }

    public function getPDOConnection() {
        if(is_null($this->connection)) {
            $creds = $this->service->getCredentials();
            $this->connection = new PDO($this->dsn, $creds->getUsername(), $creds->getPassword());
        }
        return $this->connection;
    }

    public function getDSN() {
        return $this->dsn;
    }

    private function constructDSN() {
        // Figure out the type of the database
        $creds = $this->service->getCredentials();
        $uriInfo = $creds->getUriInfo();

        $type = "";
        $database = "";
        $port = $creds->getPort();
        $host = $creds->getHost();
 
        if(isset($uriInfo['scheme']) && !empty($uriInfo['scheme'])) {
            $type = $this->parseType($uriInfo['scheme']);
        }

        if(empty($type)) {
            $type = $this->parseType($this->service->getName());
        }

        if(empty($type)) {
            $type = $this->parseType($this->service->getLabel());
        }

        if(isset($uriInfo['path']) && !empty($uriInfo['path'])) {
            $database = str_replace('/','',$uriInfo['path']);
        }

        if(!empty($port)) {
            $dsn = sprintf(self::DSN_PATTERN_WITH_PORT, $type, $host, $port, $database);
        } else {
            $dsn = sprintf(self::DSN_PATTERN, $type, $host, $database);
        }

        return $dsn;
    }

    private function parseType($toParse) {
        $types = array();
        $types['mysql'] = self::DBTYPE_MYSQL;
        $types['pgsql'] = self::DBTYPE_PG;
        $types['oci'] = self::DBTYPE_ORACLE;
        $types['sqlite'] = self::DBTYPE_SQLITE;

        foreach($types as $type => $pattern) {
            if(preg_match("#.*($pattern).*#i", $toParse)) {
                return $type;
            }
        }

        return null;
    }
}