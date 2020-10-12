<?php
/**
 * @var array $options
 * @var array $plugin
 */

define('ELFINDER_IMG_PARENT_URL', \ramprasadm1986\elfinder\Assets::getPathUrl());

// run elFinder
$connector = new elFinderConnector(new \ramprasadm1986\elfinder\elFinderApi($options, $plugin));
$connector->run();