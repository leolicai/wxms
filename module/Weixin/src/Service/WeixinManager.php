<?php
/**
 * WeixinManager.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin\Service;


use Application\Service\BaseManager;
use Weixin\Entity\Client;
use Weixin\Entity\Menu;
use Weixin\Entity\Weixin;
use Weixin\Repository\ClientRepository;
use Weixin\Repository\MenuRepository;
use Weixin\Repository\WeixinRepository;


class WeixinManager extends BaseManager
{

    /**
     * @return int
     */
    public function getWeixinCount()
    {
        return $this->getWeixinRepository()->getWeixinCount();
    }

    /**
     * @param int $page
     * @param int $size
     * @return Weixin[]
     */
    public function getWeixinLimitByPage($page = 1, $size = 10)
    {
        return $this->getWeixinRepository()->getWeixinByLimitPage($page, $size);
    }

    /**
     * @param $wxID
     * @return null|object|Weixin
     */
    public function getWeixin($wxID)
    {
        return $this->getWeixinRepository()->find($wxID);
    }


    /**
     * @param $appid
     * @return null|object|Weixin
     */
    public function getWeixinByAppID($appid)
    {
        return $this->getWeixinRepository()->findOneBy(['wxAppID' => $appid]);
    }


    /**
     * @param $clientID
     * @return null|object|Client
     */
    public function getClient($clientID)
    {
        return $this->getClientRepository()->find($clientID);
    }


    /**
     * @param $menuID
     * @return null|object|Menu
     */
    public function getMenu($menuID)
    {
        return $this->getMenuRepository()->find($menuID);
    }



    /**
     * @param Menu $menu
     */
    public function saveModifiedMenu(Menu $menu)
    {
        $this->getEntityManager()->persist($menu);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Menu $menu
     */
    public function removeMenu(Menu $menu)
    {
        $this->getEntityManager()->remove($menu);
        $this->getEntityManager()->flush();
    }



    /**
     * @param Client $client
     */
    public function saveModifiedClient(Client $client)
    {
        $this->getEntityManager()->persist($client);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Client $client
     */
    public function removeClient(Client $client)
    {
        $this->getEntityManager()->remove($client);
        $this->getEntityManager()->flush();
    }



    /**
     * @param Weixin $weixin
     */
    public function saveModifiedWeixin(Weixin $weixin)
    {
        $this->getEntityManager()->persist($weixin);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Weixin $weixin
     */
    public function removeWeixin(Weixin $weixin)
    {
        $this->getEntityManager()->remove($weixin);
        $this->getEntityManager()->flush();
    }




    /**
     * @return WeixinRepository | \Doctrine\ORM\EntityRepository
     */
    private function getWeixinRepository()
    {
        return $this->getEntityManager()->getRepository(Weixin::class);
    }

    /**
     * @return ClientRepository | \Doctrine\ORM\EntityRepository
     */
    private function getClientRepository()
    {
        return $this->getEntityManager()->getRepository(Client::class);
    }

    /**
     * @return MenuRepository | \Doctrine\ORM\EntityRepository
     */
    private function getMenuRepository()
    {
        return $this->getEntityManager()->getRepository(Menu::class);
    }
}