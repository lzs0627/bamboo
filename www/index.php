<?php
define('DS', DIRECTORY_SEPARATOR);
//
define('LIB_ROOT', dirname(dirname(__FILE__)) . DS . 'src');
//
define('WWW_ROOT', __DIR__);

define('APP_ROOT', dirname(dirname(__FILE__)) . DS. 'app');
define('APP_ACTION_ROOT', APP_ROOT . DS . 'actions');
define('APP_LAYOUT_ROOT', APP_ROOT . DS . 'layouts');
define('APP_CONFIG_ROOT', dirname(dirname(__FILE__)) . DS. 'config');

require APP_ROOT . DS . 'bootstrap.php';

try {
    \IQnote\Bamboo\Dispatcher::getInstance()->dispatch();
} catch (\Exception $e) {
    $controller = new \IQnote\Bamboo\Controller\Controller(
        APP_ACTION_ROOT . DS . 'error' . DS . 'notfound.php',
        'notfound',
        array(
            'errorMsg' => $e->getMessage()
        )
    );
    $controller->reponse();
}
