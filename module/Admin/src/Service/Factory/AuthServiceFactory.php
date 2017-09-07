<?php
/**
 * AuthServiceFactory.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/28
 * Version: 1.0
 */

namespace Admin\Service\Factory;


use Admin\Service\AuthAdapter;
use Admin\Service\AuthService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session as AuthSessionStorage;


class AuthServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionManager = $container->get(SessionManager::class);
        $authStorage = new AuthSessionStorage('Admin_Auth', 'identity', $sessionManager);
        $authAdapter = $container->get(AuthAdapter::class);

        return new AuthService($authStorage, $authAdapter);
    }

}