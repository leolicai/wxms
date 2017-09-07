<?php
/**
 * application.global.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

return [

    'logger' => [
        'writers' => [
            'default' => [
                'options' => [
                    'stream' => __DIR__ . '/../../data/logs/php-log-' . date('Ymd') . '.txt',
                ],
                'filters' => [
                    'priority' => [
                        'options' => [
                            'priority' => 7,
                        ],
                    ],
                ],
            ],
        ],
    ],

    'session_config' => [
        'cookie_lifetime' => 3600 * 24, // 1hour
        'gc_maxlifetime' => 60 * 60 * 24 * 30, // 30days

        'save_handler' => 'files',
        'save_path' => '/tmp',
    ],

    'session_manager' => [ // Session manager configuration.
        'validators' => [
            \Zend\Session\Validator\RemoteAddr::class,
            //\Zend\Session\Validator\HttpUserAgent::class,
        ],
    ],

    'session_storage' => [ // Session storage configuration.
        'type' => \Zend\Session\Storage\SessionArrayStorage::class,
    ],

    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'driverClass' => \Doctrine\DBAL\Driver\PDOMySql\Driver::class,
                    'host' => '',
                    'port' => '',
                    'user' => '',
                    'password' => '',
                    'unix_socket' => '',
                    'dbname' => '',
                    'charset' => 'utf8mb4',
                    'defaultTableOptions' => [
                        'collate' => 'utf8mb4_unicode_ci',
                        'charset' => 'utf8mb4',
                        'engine' => 'InnoDB',
                    ],
                ],
            ],
        ],

        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/DoctrineORMModule/Migrations',
                'name' => 'Doctrine Database Migrations',
                'namespace' => 'DoctrineORMModule\Migrations',
                'table' => 'migrations',
                'column'    => 'version',
            ],
        ],
    ],
];