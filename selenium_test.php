<?php

require_once __DIR__ . "/vendor/autoload.php";

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

$serverUrl = 'http://localhost:4444';

// Chrome
$capabilities = DesiredCapabilities::chrome();
$driver = RemoteWebDriver::create($serverUrl, $desiredCapabilities);

$driver->get('https://en.wikipedia.org/wiki/Selenium_(software)');

$driver->quit();