<?php
/**
 * WeixinRepository.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/9/7
 * Version: 1.0
 */

namespace Weixin\Repository;


use Doctrine\ORM\EntityRepository;
use Weixin\Entity\Weixin;


class WeixinRepository extends EntityRepository
{

    /**
     * @return integer
     */
    public function getWeixinCount()
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->from(Weixin::class, 'w');
        $queryBuilder->select($queryBuilder->expr()->count('w.wxID'));

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }


    /**
     * @param int $page
     * @param int $size
     * @return Weixin[]
     */
    public function getWeixinByLimitPage($page = 1, $size = 100)
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('w')
            ->from(Weixin::class, 'w')
            ->setMaxResults($size)
            ->setFirstResult(($page - 1) * $size)
            ->orderBy('w.wxID', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }


}