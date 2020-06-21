<?php

namespace ApigeeKongUtil;

use ApigeeKongUtil\ApigeeBase;

/**
 * Class ApigeeProxy
 * @package ApigeeKongUtil
 */
class ApigeeAppDev extends ApigeeBase {

    function __construct() {
        parent::__construct();
    }

    public function getAppDev() {
        // Get the list of Apps.
        $appListUrl = str_replace('{organization}', $this->apigeeConfig['organization'], $this->apigeeConfig['appList']);
        $appListUrl = $this->apigeeConfig['url'] . $appListUrl;
        $results = $this->http->getData($appListUrl, $this->credentials);
        $data = [];
        foreach ($results as $result) {
            $appDetailUrl =  $appListUrl . '/' . $result;
            $data[] = $this->http->getData($appDetailUrl, $this->credentials);
        }
     return $data;
    }
}