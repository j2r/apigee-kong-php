<?php

namespace ApigeeKongUtil;

use ApigeeKongUtil\KongBase;

class KongPlugin extends KongBase {
    function __construct() {
        parent::__construct();
    }

    public function processPolicyQuota($policyData, $routeResult, $url) {
        $unit = 'config.' . $policyData['TimeUnit'];
        $data['name'] =  'rate-limiting';
        $data[$unit] = $policyData['Allow']['@attributes']['count'];
        $result = $this->http->postData($url, $data, 'body');
    }

    public function processPolicyVerifyAPIKey($policyData, $routeResult, $url) {
        $data['name'] =  'key-auth';
        // @todo : This apply the keyauth in get argument but need to check how this is applied in Apigee and process accordingly.
        $result = $this->http->postData($url, $data, 'body');
    }
}