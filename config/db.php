<?php

return array(
    'default'=>array(
        'type' => 'pdo',
        'config' => array(
            'connection'=>array(
                'dsn'        => 'mysql:dbname=xxxx;host=xxx.xxx.xxx.xxx;port=3306',
                'username'   => 'xxxxxxx',
                'password'   => 'xxxxxxx'
            ),
            'charset' => 'utf8'
        )
    ),
    //update
    'update' => array(
        'type' => 'pdo',
        'config' => array(
            'connection'=>array(
                'dsn'        => 'mysql:dbname=xxxx;host=xxx.xxx.xxx.xxx;port=3306',
                'username'   => 'xxxxxxx',
                'password'   => 'xxxxxxx'
            ),
            'charset' => 'utf8'
        )
    ),
    //read
    'read' => array(
        'type' => 'pdo',
        'config' => array(
            'connection'=>array(
                'dsn'        => 'mysql:dbname=xxxx;host=xxx.xxx.xxx.xxx;port=3306',
                'username'   => 'xxxxxxx',
                'password'   => 'xxxxxxx'
            ),
            'charset' => 'utf8'
        )
    ),
);
