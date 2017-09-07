<?php
/**
 * module.config.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 17/8/27
 * Version: 1.0
 */

namespace Admin;

use Application\Service\Factory\EntityManagerFactory;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;


return [

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\DashboardController::class => InvokableFactory::class,
            Controller\ProfileController::class => InvokableFactory::class,
            Controller\AdminerController::class => InvokableFactory::class,
        ],
    ],


    'service_manager' => [
        'factories' => [
            Service\AuthAdapter::class => InvokableFactory::class,
            Service\AuthService::class => Service\Factory\AuthServiceFactory::class,

            Service\MenuManager::class => Service\Factory\MenuManagerFactory::class,

            Service\AdminerManager::class => EntityManagerFactory::class,
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


    'view_helpers' => [
        'factories' => [
            View\Helper\TopRightMenu::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\SideTreeMenu::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\PageTitleBar::class => InvokableFactory::class,
        ],
        'aliases' => [
            'adminTopRightMenu' => View\Helper\TopRightMenu::class,
            'adminSideTreeMenu' => View\Helper\SideTreeMenu::class,
            'adminPageTitleBar' => View\Helper\PageTitleBar::class,
        ],
    ],


    'view_manager' => [
        'template_map' => [
            'layout/admin_simple'  => __DIR__ . '/../view/layout/simple.phtml',
            'layout/admin_layout' => __DIR__ . '/../view/layout/layout.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],


    'router' => [
        'routes' => [
            'admin' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/admin[/]',
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

                    'dashboard' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'dashboard[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                                'key' => '[a-zA-Z0-9_\-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\DashboardController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],

                    'profile' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'profile[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                                'key' => '[a-zA-Z0-9_\-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\ProfileController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],

                    'adminer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'adminer[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                                'key' => '[a-zA-Z0-9_\-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\AdminerController::class,
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