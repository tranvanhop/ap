<?php

return array(
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=ap.tranvanhop;host=localhost',
//        'dsn' => 'mysql:dbname=ap.tranvanhop;host=127.0.0.1;port=3307',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
            => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'mail' => array(
        'transport' => array(
            'options' => array(
                'host'              => 'smtp.gmail.com',
                'connection_class'  => 'plain',
                'connection_config' => array(
                    'username' => 'leehop.blog',
                    'password' => 'khongdean2126',
                    'ssl' => 'tls'
                ),
            ),
        ),
    )
);