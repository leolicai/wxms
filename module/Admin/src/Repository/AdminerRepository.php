<?php
/**
 * AdminerRepository.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/30
 * Version: 1.0
 */

namespace Admin\Repository;


use Admin\Entity\Adminer;
use Doctrine\ORM\EntityRepository;


class AdminerRepository extends EntityRepository
{

    /**
     * @return integer
     */
    public function getAdminersCount()
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->from(Adminer::class, 'a');
        $queryBuilder->select($queryBuilder->expr()->count('a.adminID'));

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }


    /**
     * @param int $page
     * @param int $size
     * @return Adminer[]
     */
    public function getAdminersByLimitPage($page = 1, $size = 100)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('a')
            ->from(Adminer::class, 'a')
            ->setMaxResults($size)
            ->setFirstResult(($page - 1) * $size)
            ->orderBy('a.adminDefault', 'DESC')
            ->addOrderBy('a.adminLevel', 'DESC')
            ->addOrderBy('a.adminCreated', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }

}