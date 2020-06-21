<?php

namespace ApigeeKongUtil;

use ApigeeKongUtil\FileProcessor;
use ApigeeKongUtil\HttpProcessor;

class KongBase {
    protected $kongConfig;
    Protected $file;
    Protected $http;
    function __construct() {
        $configFile = CONFIG_PATH . 'kong.yml';

        $this->file = new FileProcessor();
        $this->http = new HttpProcessor();

        $this->kongConfig = $this->file->getYaml($configFile);
        $this->kongConfig['serviceUrl'] = $this->kongConfig['url'] . 'services';
    }
}