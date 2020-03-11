<?php

namespace PHPCFEnv\CFEnv;

class CFVolume {

    const MODE_READ_ONLY = 'r';
    const MODE_READ_WRITE = 'rw';

    private $volumeData = array();

    public function __construct(array $jsonArray) {
        $this->volumeData = $jsonArray;
    }

    public function getMap() {
        return $this->volumeData;
    }

    public function getPath() {
        return $this->volumeData["container_dir"];
    }

    public function getMode() {
        if($this->volumeData["mode"] == self::MODE_READ_ONLY) {
            return self::MODE_READ_ONLY;
        }

        return self::MODE_READ_WRITE;
    }

}