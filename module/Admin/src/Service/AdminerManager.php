<?php
/**
 * AdminerManager.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/29
 * Version: 1.0
 */

namespace Admin\Service;


use Admin\Entity\Adminer;
use Admin\Repository\AdminerRepository;
use Application\Service\BaseManager;
use Doctrine\ORM\EntityManager;


class AdminerManager extends BaseManager
{

    /**
     * @return AdminerRepository | \Doctrine\ORM\EntityRepository
     */
    private function getAdminerRepository()
    {
        return $this->getEntityManager()->getRepository(Adminer::class);
    }

    /**
     * @param $adminID
     * @return Adminer|null|object
     */
    public function getAdminerByID($adminID)
    {
        return $this->getAdminerRepository()->find($adminID);
    }


    /**
     * @param $email
     * @return Adminer|object|null
     */
    public function getAdminerByEmail($email)
    {
        return $this->getAdminerRepository()->findOneBy(['adminEmail' => $email]);
    }


    /**
     * @return integer
     */
    public function getAdminersCount()
    {
        return $this->getAdminerRepository()->getAdminersCount();
    }

    /**
     * @param int $page
     * @param int $size
     * @return Adminer[]
     */
    public function getAdminersByLimitPage($page = 1, $size = 100)
    {
        return $this->getAdminerRepository()->getAdminersByLimitPage($page, $size);
    }

    /**
     * @return Adminer[]
     */
    public function getAllAdminers()
    {
        return $this->getAdminerRepository()->findBy(['adminDefault' => Adminer::DEFAULT_OTHER]);
    }


    /**
     * @param Adminer $adminer
     */
    public function saveModifiedAdminer(Adminer $adminer)
    {
        $this->getEntityManager()->persist($adminer);
        $this->getEntityManager()->flush();
    }
}