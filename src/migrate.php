<?php

require __DIR__ . '/vendor/autoload.php';
use ApigeeKongUtil\ApigeeProxy;
use ApigeeKongUtil\ApigeeAppDev;
use ApigeeKongUtil\KongRoute;
use ApigeeKongUtil\KongConsumer;

define ('BASE_PATH', __DIR__);
define ('CONFIG_PATH', realpath('../config') . '/');
define ('DATA_PATH', realpath('../data') . '/');

$apigee = new ApigeeProxy();
$kong = new KongRoute();


// Download the list of proxies.
$proxyCount = $apigee->writeProxy();
if (is_numeric($proxyCount)) {
    echo "Available Proxies are : $proxyCount" . PHP_EOL;
}
else {
    echo "Something went wrong, Please check logs for more information";die();
}

// Get the last revision of the proxies.
$proxyRevisions = $apigee->writeProxyRevision();
foreach ($proxyRevisions as $name => $revisionNumber) {
    echo "Proxy : $name . mapped revision " . $revisionNumber . PHP_EOL;
}

// Download the last revision of the proxy.
$proxyDownloads = $apigee->downloadProxyRevision();
echo "Proxies download activity is complete" . PHP_EOL;

// Import Downloaded latest revision of Apigee into Kong.
$kongRoutesServices = $kong->migrateRoutesServices();



// Get list of APP of Apigee and migrate to Kong
$apigeeAppDev = new ApigeeAppDev();
$appDevData = $apigeeAppDev->getAppDev();

$kongConsumer = new KongConsumer();
$consumers = $kongConsumer->migrateConsumer($appDevData);
foreach ($consumers as $consumer) {
    echo $consumer . " has been migrated" . PHP_EOL;
}

