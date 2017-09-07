<?php
/**
 * MenuManagerFactory.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/29
 * Version: 1.0
 */

namespace Admin\Service\Factory;


use Admin\Service\AdminerManager;
use Admin\Service\AuthService;
use Admin\Service\ComponentManager;
use Admin\Service\GroupManager;
use Admin\Service\MenuManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;


class MenuManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService = $container->get(AuthService::class);
        $adminerManager = $container->get(AdminerManager::class);
        $helperManager = $container->get('ViewHelperManager');
        $urlHelper = $helperManager->get('url');

        return new MenuManager($urlHelper, $authService, $adminerManager);
    }
}