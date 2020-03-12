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

use \PDO;

class CFPDOBinding extends CFBinding {

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

    /**
     * Bind to the given service instance.
     * Throw an exception if the service instance is not appropriate
     * @throw CFUnsupportedBindingException 
     */
    public function bind(CFService $service) {
        if(!$service->hasTag('relational')) {
            throw new CFUnsupportedBindingException("CFPDOBinding cannot be bound to the service '".$service->getName());
        }
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