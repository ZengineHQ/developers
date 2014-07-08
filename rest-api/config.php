<?php

$apiDomain = 'https://api.wizehive.com';
$authDomain = 'https://auth.wizehive.com';
$demoClientId = 'rest-api-quick-start-demo';

$localConfigFile = dirname(__FILE__) .'/config.local.php';
if (file_exists($localConfigFile) and is_readable($localConfigFile)) {
    require $localConfigFile;
}