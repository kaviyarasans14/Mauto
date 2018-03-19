<?php

namespace Mautic\SubscriptionBundle\Entity;

use Doctrine\ORM\EntityManager;

class SubscriptionRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->getEntityManager()->getConnection();
    }

    public function getPlanInfo($provider, $planname, $plancycle)
    {
        $qb = $this->getConnection()->createQueryBuilder();

        $qb->select('pl.planid')
            ->from(MAUTIC_TABLE_PREFIX.'planinfo', 'pl');
        $qb->andWhere('pl.provider = :provider')
            ->setParameter('provider', $provider);
        $qb->andWhere('pl.planname = :planname')
            ->setParameter('planname', $planname);
        $qb->andWhere('pl.plancycle = :plancycle')
            ->setParameter('plancycle', $plancycle);

        return $qb->execute()->fetchAll();
    }
}
