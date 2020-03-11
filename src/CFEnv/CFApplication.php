<?php

namespace PHPCFEnv\CFEnv;

class CFApplication {

    private $applicationData = array();


    public function __construct(array $jsonArray) {
        $this->applicationData = $jsonArray;
    }
    
    public function getMap() {
		return $this->applicationData;
	}

	public function getSpaceId() {
		return $this->getString("space_id");
	}

	public function getSpaceName() {
		return $this->getString("space_name");
	}

	public function getInstanceId() {
		return $this->getString("instance_id");
	}

	public function getInstanceIndex() {
		return $this->getInt("instance_index");
	}

	public function getHost() {
		return $this->getString("host");
	}

	public function getPort() {
		return $this->getInt("port");
	}

	public function getCfApi() {
		return $this->getString("cf_api");
	}

	public function getApplicationId() {
		return $this->getString("application_id");
    }
    
	public function getApplicationVersion() {
		$version = $this->getString("application_version");
		if ($version != null) {
			return $version;
		}
		return $this->getString("version");
	}

	public function getApplicationName() {
		return $this->getString("application_name");
	}

	public function getApplicationUris() {
		return $this->getStringArray("application_uris");
	}

	public function getVersion() {
		return $this->getString("version");
	}

	public function getName() {
		return $this->getString("name");
	}

	public function getUris() {
		return $this->getStringArray("uris");
	}

	private function getString($key) {
		if ($this->applicationData != null && array_key_exists($key, $this->applicationData)) {
			return $this->applicationData[$key];
		}
		return null;
	}

	private function getStringArray($key) {
		if ($this->applicationData != null && array_key_exists($key, $this->applicationData)) {
			$value = $this->applicationData[$key];
			if(is_array($value)) {
				return $value;
			}
		}
		return null;
	}

	private function getInt($key) {
		if ($this->applicationData != null && array_key_exists($key, $this->applicationData)) {
			$intValue = $this->applicationData[$key];
			if(is_int($intValue)) {
				return $intValue;
			}
		}
		return -1;
	}
}