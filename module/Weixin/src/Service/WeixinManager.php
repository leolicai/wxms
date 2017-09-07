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
     * @return WeixinRepository | \Doctrine\ORM\EntityRepository
     */
    private function getWeixinRepository()
    {
        return $this->getEntityManager()->getRepository(Weixin::class);
    }
}