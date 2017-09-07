<?php
/**
 * MenuFactory.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/29
 * Version: 1.0
 */

namespace Admin\View\Helper\Factory;


use Admin\Service\MenuManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;


class MenuFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        $menuManager = $container->get(MenuManager::class);

        $items = [];

        if(preg_match('/TopRightMenu$/', $requestedName)) {
            $items = $menuManager->getTopRightItems();
        }

        if(preg_match('/SideTreeMenu/', $requestedName)) {
            $items = $menuManager->getSideTreeItems();
        }

        return new $requestedName($items);
    }

}