<?php

namespace ApigeeKongUtil;

use ApigeeKongUtil\FileProcessor;
use ApigeeKongUtil\HttpProcessor;

class ApigeeBase {
    protected $apigeeConfig;
    protected $credentials;
    Protected $file;
    Protected $http;
    function __construct() {
        $configFile = CONFIG_PATH . 'apigee.yml';

        $this->file = new FileProcessor();
        $this->http = new HttpProcessor();

        $this->apigeeConfig = $this->file->getYaml($configFile);
        $this->credentials = base64_encode($this->apigeeConfig['username'] . ':' . $this->apigeeConfig['password']);
    }
}