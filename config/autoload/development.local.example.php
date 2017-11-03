<?php
/**
 * development.local.example.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

return [

    'weixin' => [
        'api' => [
            'domain' => '',
        ],
    ],


    'logger' => [
        'writers' => [
            'default' => [
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
        'save_handler' => 'files',
        'save_path' => __DIR__ . '/../../data/sessions', //'/tmp',
    ],


    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'host' => 'localhost',
                    'port' => '3306',
                    'user' => 'root',
                    'password' => 'root',
                    'dbname' => 'test',
                    'unix_socket' => '/Applications/MAMP/tmp/mysql/mysql.sock',
                ],
            ],
        ],
    ],

];