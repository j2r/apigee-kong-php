<?php

namespace ApigeeKongUtil;

use ApigeeKongUtil\KongBase;
use ApigeeKongUtil\KongPlugin;

class KongRoute extends KongBase {
    function __construct() {
        parent::__construct();
    }

    /**
     * @todo - Breakdown the function to micro.
     */
    public function migrateRoutesServices() {
        // get Apigee list of proxy and stored directory path.
        $apigee = new ApigeeProxy();
        $kongPlugin = new KongPlugin();
        $apigeeProxies = $apigee->getapigeeProxiesDownload();
        foreach ($apigeeProxies as $proxy => $path) {

            $proxyName = str_replace('%20', ' ', $proxy);
            $basexml = $this->file->getXMLData($path . "/$proxyName.xml");

            if (empty($basexml['TargetEndpoints']['TargetEndpoint'])) {
                echo "Proxy : " . $proxyName . " can not be migrated as Target Endpoint is not available." . PHP_EOL;
                continue;

            }

            $target = $basexml['TargetEndpoints']['TargetEndpoint'] . '.xml';
            $targetxml = $this->file->getXMLData("$path/targets/$target");
            if (empty($targetxml['HTTPTargetConnection']['URL'])) {
                echo "Proxy : " . $proxyName . " can not be migrated as HTTP Target Connection URL is not available." . PHP_EOL;
                continue;
            }

            $proxy = $basexml['ProxyEndpoints']['ProxyEndpoint'] . '.xml';
            $proxyxml = $this->file->getXMLData("$path/proxies/$target");

            $serviceData = [
                'name' => $proxyName,
                'url' => $targetxml['HTTPTargetConnection']['URL']
            ];

            $routeData = [
                'hosts' => [$proxyName],
                'paths' => [$proxyxml['HTTPProxyConnection']['BasePath']]
            ];
            $serviceResult = $this->http->postData($this->kongConfig['serviceUrl'], $serviceData);
            $routeResult = $this->http->postData($this->kongConfig['serviceUrl'] . "/$proxyName/routes", $routeData);

            // Not to process Plugins if Services or Routes are empty or having an error.
            if (empty($routeResult) || empty($serviceResult)) {
                echo "Proxy : " . $proxyName . " can not be migrated. Check error logs for more details." . PHP_EOL;
                continue;
            }
            $routePoliciesUrl = $this->kongConfig['url'] . 'routes/' .$routeResult['id'] . '/plugins';

            // @todo : need to enhance the Policy migration to postflow and target.
            if (isset($proxyxml['PreFlow'])) {
                foreach ($proxyxml['PreFlow']['Request']['Step'] as $policy) {
                    $policyName = $policy['Name'];
                    $policyData = $this->file->getXMLData("$path/policies/$policyName.xml");
                    $policyProcessor = 'processPolicy' . $policyData['name'];
                    if (method_exists($kongPlugin, $policyProcessor)) {
                        $return =  $kongPlugin->$policyProcessor($policyData, $routeResult, $routePoliciesUrl);
                    }
                }
            }
            // @todo - Implement target prefix and suffix plugins
            echo "Proxy : " . $proxyName . " imported successfully" . PHP_EOL;
        }
    }

    public function deleteData($type) {
        $decodedPayload = $this->http->getData($this->kongConfig['url'] . $type);
        foreach ($decodedPayload['data'] as $route) {
            $id = $route['id'];
            $this->http->deleteData($this->kongConfig['url'] . $type . '/' . $id);
            echo "Delete $type ID : $id" . PHP_EOL;
        }
    }
}