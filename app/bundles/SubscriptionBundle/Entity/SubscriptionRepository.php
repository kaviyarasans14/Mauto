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

    public function getSignupInfo($emailid)
    {
        $qb = $this->getConnection()->createQueryBuilder();

        $qb->select('al.f11', 'al.f2', 'al.f5', 'al.appid')
            ->from(MAUTIC_TABLE_PREFIX.'applicationlist', 'al');
        $qb->andWhere('al.f4 = :email')
            ->setParameter('email', $emailid);

        return $qb->execute()->fetchAll();
    }
}
