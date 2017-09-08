<?php
/**
 * module.config.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin;

use Application\Service\Factory\EntityManagerFactory;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;


return [

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\MessageController::class => InvokableFactory::class,
            Controller\ApiController::class => InvokableFactory::class,
        ],
    ],


    'service_manager' => [
        'factories' => [
            Service\WeixinManager::class => EntityManagerFactory::class,
            Service\WeixinService::class => Service\Factory\WeixinServiceFactory::class,
        ],
    ],

    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'wx_mp_verify' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/MP_verify_[:key].txt',
                    'constraints' => [
                        'key' => '[a-zA-Z0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'wx-mp-verify',
                    ],
                ],
            ],

            'weixin' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/weixin[/]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [

                    'index' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'index[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                                'key' => '[a-zA-Z0-9_\-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],

                    'message' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'dashboard[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                                'key' => '[a-zA-Z0-9_\-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\MessageController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],

                    // TBM
                ],
            ],


        ],
    ],

];