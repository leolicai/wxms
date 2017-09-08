<?php
/**
 * WeixinServiceFactory.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/8
 * Version: 1.0
 */

namespace Weixin\Service\Factory;


use Interop\Container\ContainerInterface;
use Weixin\Service\WeixinManager;
use Weixin\Service\WeixinService;
use Zend\ServiceManager\Factory\FactoryInterface;

class WeixinServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $weixinManager = $container->get(WeixinManager::class);

        return new WeixinService($weixinManager);
    }


}