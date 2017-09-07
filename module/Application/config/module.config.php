<?php
/**
 * module.config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application;


use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;


return [
    'router' => [
        'routes' => [
            'default' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],

            'application' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/application[/]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'index' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => 'index[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                                'key' => '[a-zA-Z0-9_\-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    // TBC
                ],
            ],
        ],
    ],


    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],

    'controller_plugins' => [
        'factories' => [
            Controller\Plugin\AppConfigPlugin::class => Controller\Plugin\Factory\AppConfigPluginFactory::class,
            Controller\Plugin\AppLoggerPlugin::class => Controller\Plugin\Factory\AppLoggerPluginFactory::class,
            Controller\Plugin\AppAsyncPlugin::class => InvokableFactory::class,
            Controller\Plugin\AppServerPlugin::class => InvokableFactory::class,
        ],
        'aliases' => [
            'appConfig' => Controller\Plugin\AppConfigPlugin::class,
            'appLogger' => Controller\Plugin\AppLoggerPlugin::class,
            'appAsync' => Controller\Plugin\AppAsyncPlugin::class,
            'appServer' => Controller\Plugin\AppServerPlugin::class,
        ],
    ],


    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],


    'view_helpers' => [
        'factories' => [
            View\Helper\Pagination::class => InvokableFactory::class,
        ],
        'aliases' => [
            'pagination' => View\Helper\Pagination::class,
        ],
    ],

];