<?php
/**
 * Module.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 17/8/27
 * Version: 1.0
 */

namespace Admin;


use Admin\Controller\DashboardController;
use Admin\Controller\IndexController;
use Admin\Controller\ProfileController;
use Admin\Entity\Action;
use Admin\Entity\Adminer;
use Admin\Exception\RuntimeException;
use Admin\Service\AdminerManager;
use Admin\Service\AuthService;
use Admin\Service\ComponentManager;
use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;


class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }


    /**
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $sharedEventManager = $event->getApplication()->getEventManager()->getSharedManager();
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatchListener'], 100);
    }


    /**
     * @param MvcEvent $event
     */
    public function onDispatchListener(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();
        $serviceManager->get(SessionManager::class); //Init session manager

        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $controllerName = str_replace('-', '', ucfirst(ucwords($controllerName, '-')));

        if($controllerName == IndexController::class) { // Allow all access
            $event->getViewModel()->setTemplate('layout/admin_simple');
            return ;
        }

        // Login status validate
        $authService = $serviceManager->get(AuthService::class);
        if (!$authService->hasIdentity()) {
            //$event->getViewModel()->setTemplate('layout/admin_simple');
            throw new RuntimeException('使用本模块需要您先登录系统.');
        }

        $event->getViewModel()->setTemplate('layout/admin_layout');

        if (empty($authService->getIdentity())) {
            throw new  RuntimeException("请重新登入系统!");
        }
    }


}