<?php
require LIB_ROOT . DS . 'bootstrap.php';

$envName = getenv('APP_ENVIRONMENT');

if (! $envName) {
    throw new \Exception('APP_ENVIRONMENT not defined.');
}

define('APP_ENVIRONMENT', $envName);

Config::setup(APP_CONFIG_ROOT, APP_ENVIRONMENT);
