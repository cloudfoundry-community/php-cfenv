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

class CFLimits {

    private $limitsData;

    public function __construct(array $data) {
        $this->limitsData = $data;
    }

    public function getMem() {
        return $this->getInt('mem');
    }

    public function getDisk() {
        return $this->getInt('disk');
    }

    public function getFds() {
        return $this->getInt('fds');
    }

    private function getInt($key) {
		if ($this->limitsData != null && array_key_exists($key, $this->limitsData)) {
			$intValue = $this->limitsData[$key];
			if(is_int($intValue)) {
				return $intValue;
			}
		}
		return -1;
	}
}