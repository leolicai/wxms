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

    'weixin' => [
        'api' => [
            'domain' => 'http://' . $_SERVER['HTTP_HOST'],
        ],
        'menu' => [
            'type' => [
                'click' => '[click] 推送按钮事件',
                'view' => '[view] 打开URL网页',
                'miniprogram' => '[miniprogram] 打开微信小程序',
                'scancode_push' => '[scancode_push] 打开扫码功能',
                'scancode_waitmsg' => '[scancode_waitmsg] 打开扫码并咨询用户',
                'pic_sysphoto' => '[pic_sysphoto] 打开相机拍照',
                'pic_photo_or_album' => '[pic_photo_or_album] 打开拍照或相册选择器',
                'pic_weixin' => '[pic_weixin] 打开微信相册',
                'location_select' => '[location_select] 打开地理位置',
                'media_id' => '[media_id] 读取素材信息',
                'view_limited' => '[view_limited] 打开素材URL',
            ],
            'language' => [
                'zh_CN' => '简体中文',
                'zh_TW' => '繁体中文TW',
                'zh_HK' => '繁体中文HK',
                'en' => '英文',
                'id' => '印尼',
                'ms' => '马来',
                'es' => '西班牙',
                'ko' => '韩国',
                'it' => '意大利',
                'ja' => '日本',
                'pl' => '波兰',
                'pt' => '葡萄牙',
                'ru' => '俄国',
                'th' => '泰文',
                'vi' => '越南',
                'ar' => '阿拉伯语',
                'hi' => '北印度',
                'he' => '希伯来',
                'tr' => '土耳其',
                'de' => '德语',
                'fr' => '法语',
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
                            'route' => 'message[/:action[/:key]][:suffix]',
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

                    'api' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'api[/:action[/:wx[/:client]]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                                'wx' => '[0-9]+',
                                'client' => '[a-zA-Z0-9_\-\=]+',
                                'suffix' => '(/|.json|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\ApiController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],

                ],
            ],


        ],
    ],

];