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
use Weixin\Entity\Weixin;
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
}