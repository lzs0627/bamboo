<?php
/**
 * Bamboo test bootstrap file - sets up a test environment.
 */

namespace IQnote\Bamboo;

$loader = require __DIR__ . '/../vendor/autoload.php';

define('BAMBOO_CONFIG_PATH', realpath(__DIR__.'/../config/'));
define('BAMBOO_ENV', 'test');

Config::setup(BAMBOO_CONFIG_PATH, BAMBOO_ENV);
