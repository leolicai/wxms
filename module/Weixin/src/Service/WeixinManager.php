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
use Weixin\Entity\Event;
use Weixin\Entity\Menu;
use Weixin\Entity\QRCode;
use Weixin\Entity\Tag;
use Weixin\Entity\Weixin;
use Weixin\Repository\ClientRepository;
use Weixin\Repository\EventRepository;
use Weixin\Repository\MenuRepository;
use Weixin\Repository\QRCodeRepository;
use Weixin\Repository\TagRepository;
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
     * @param $id
     * @return null|object|Menu
     */
    public function getMenu($id)
    {
        return $this->getMenuRepository()->find($id);
    }

    /**
     * @param $id
     * @return null|object|Tag
     */
    public function getTag($id)
    {
        return $this->getTagRepository()->find($id);
    }


    /**
     * @param $id
     * @return null|object|QRCode
     */
    public function getQRCode($id)
    {
        return $this->getQRCodeRepository()->find($id);
    }

    /**
     * @param $eventID
     * @return null|object|Event
     */
    public function getEvent($eventID)
    {
        return $this->getEventRepository()->find($eventID);
    }


    /**
     * @param Weixin $wx
     * @param string $type
     * @param string $target
     * @return Event|null|Object
     */
    public function getWeixinEvent(Weixin $wx, $type, $target)
    {
        return $this->getEventRepository()->findOneBy(['eventWeixin' => $wx, 'eventType' => $type, 'eventTarget' => $target]);
    }


    /**
     * @param Event $event
     */
    public function saveModifiedEvent(Event $event)
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Event $event
     */
    public function removeEvent(Event $event)
    {
        $this->getEntityManager()->remove($event);
        $this->getEntityManager()->flush();
    }


    /**
     * @param QRCode $qrcode
     */
    public function saveModifiedQRCode(QRCode $qrcode)
    {
        $this->getEntityManager()->persist($qrcode);
        $this->getEntityManager()->flush();
    }

    /**
     * @param QRCode $qrcode
     */
    public function removeQRCode(QRCode $qrcode)
    {
        $this->getEntityManager()->remove($qrcode);
        $this->getEntityManager()->flush();
    }


    /**
     * @param Tag $tag
     */
    public function saveModifiedTag(Tag $tag)
    {
        $this->getEntityManager()->persist($tag);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->getEntityManager()->remove($tag);
        $this->getEntityManager()->flush();
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

    /**
     * @return TagRepository | \Doctrine\ORM\EntityRepository
     */
    private function getTagRepository()
    {
        return $this->getEntityManager()->getRepository(Tag::class);
    }


    /**
     * @return QRCodeRepository | \Doctrine\ORM\EntityRepository
     */
    private function getQRCodeRepository()
    {
        return $this->getEntityManager()->getRepository(QRCode::class);
    }


    /**
     * @return EventRepository | \Doctrine\ORM\EntityRepository
     */
    private function getEventRepository()
    {
        return $this->getEntityManager()->getRepository(Event::class);
    }
}