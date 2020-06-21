<?php

namespace ApigeeKongUtil;

use ApigeeKongUtil\ApigeeBase;

/**
 * Class ApigeeProxy
 * @package ApigeeKongUtil
 */
class ApigeeProxy extends ApigeeBase {

    protected $proxyFilePath = DATA_PATH . 'apigeeProxies.yml';
    protected $proxyRevisionFilePath = DATA_PATH . 'apigeeProxiesRevision.yml';
    protected $proxyRevisionDownloadFilePath = DATA_PATH . 'apigeeProxiesDownload.yml';
    /*
     *
     */
    function __construct() {
        parent::__construct();
    }

    public function getapigeeProxiesDownload() {
        return $this->file->getYaml($this->proxyRevisionDownloadFilePath);
    }

    /*
     * Write Proxy list to YML file.
     * Return number of proxies or false.
     */
    public function writeProxy() {
        $results = $this->getProxyList();
        if (empty($results)) {
            return false;
        }
        $this->file->setYaml($this->proxyFilePath, $results);
        return count($results);
    }

    /*
     *
     */
    public function writeProxyRevision() {
        $apigeeProxies = $this->file->getYaml($this->proxyFilePath);
        $proxyRevisionUrl = $this->apigeeConfig['url'] . str_replace('{organization}', $this->apigeeConfig['organization'], $this->apigeeConfig['proxyRevision']);
        $results = [];
        foreach ($apigeeProxies as $apigeeProxy) {
            $results[$apigeeProxy] = $this->getProxyRevision($apigeeProxy);
        }
        $this->file->setYaml($this->proxyRevisionFilePath, $results);
        return $results;
    }

    public function downloadProxyRevision() {
        $proxyDownloadUrlBase = $this->apigeeConfig['url'] . str_replace('{organization}', $this->apigeeConfig['organization'], $this->apigeeConfig['proxyDownload']);
        $apigeeProxiesRevision = $this->file->getYaml($this->proxyRevisionFilePath);
        $proxyExportPaths = [];
        foreach ($apigeeProxiesRevision as $proxyRevision => $proxyRevisionNumber) {
            $proxyDownloadUrl = $proxyDownloadUrlBase;
            $proxyDownloadUrl = str_replace('{proxy}', $proxyRevision, $proxyDownloadUrl);
            $proxyDownloadUrl = str_replace('{revisionNumber}', $proxyRevisionNumber, $proxyDownloadUrl);
            $pathZip = DATA_PATH . 'proxiesData/' . $proxyRevision . '.zip';
            $pathExtract = DATA_PATH . 'proxiesData/' . $proxyRevision;
            $curl = "curl -X GET --header 'Authorization: Basic $this->credentials' '$proxyDownloadUrl?format=bundle' -o $pathZip";
            // @todo - Move this to guzzle from command line curl;
            shell_exec($curl);

            // Skip the process if Zip file does not exist.
            if (!file_exists($pathZip)) {
                continue;
            }
            // @todo - add php extension for unzip and use that instead of command.
            shell_exec("unzip $pathZip -d $pathExtract");
            $proxyExportPaths[$proxyRevision] = $pathExtract . '/apiproxy';
        }
        $this->file->setYaml($this->proxyRevisionDownloadFilePath, $proxyExportPaths);
        return $proxyExportPaths;
    }

    private function getProxyRevision($apigeeProxy) {
        $apigeeProxy = str_replace(' ', '%20', $apigeeProxy);
        $proxyRevision = $this->apigeeConfig['url'] . str_replace('{organization}', $this->apigeeConfig['organization'], $this->apigeeConfig['proxyRevision']);
        $proxyRevision = str_replace('{proxy}', $apigeeProxy, $proxyRevision);
        $response = $this->http->getData($proxyRevision, $this->credentials);
        return end($response);
    }

    private function getProxyList() {
        $proxyList = str_replace('{organization}', $this->apigeeConfig['organization'], $this->apigeeConfig['proxyList']);
        $proxyUrl = $this->apigeeConfig['url'] . $proxyList . '?count=100';
        return $this->http->getData($proxyUrl, $this->credentials);
    }
}