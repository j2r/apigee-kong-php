<?php

// NOTE : This file is for local cleanup during development an Debugging. Do not run this file on other kong environment as it will delete all the kong routes and services.

require __DIR__ . '/vendor/autoload.php';
use ApigeeKongUtil\ApigeeProxy;
use ApigeeKongUtil\KongRoute;

define ('BASE_PATH', __DIR__);
define ('CONFIG_PATH', realpath('../config') . '/');
define ('DATA_PATH', realpath('../data') . '/');

$kong = new KongRoute();

// Delete all YML files and Empty out the DATA_PATH/roxiesData
// @todo - Remove shell command and do this via PHP file object.
$ymlRemove = 'rm -rf ' . DATA_PATH . '/*.yml';
$proxiesData = 'rm -rf ' . DATA_PATH . '/proxiesData/*';
shell_exec($ymlRemove);
shell_exec($proxiesData);

// Delete all Service and Routes
$kong->deleteData('routes');
$kong->deleteData('services');
$kong->deleteData('consumers');
