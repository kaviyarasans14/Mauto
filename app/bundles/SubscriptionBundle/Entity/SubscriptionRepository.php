<?php

namespace Mautic\SubscriptionBundle\Entity;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Entity\LicenseInfoRepository;

class SubscriptionRepository
{
    /**
     * @var EntityManager
     */
    private $commondbentityManager;
    /**
     * @var LicenseInfoRepository
     */
    private $licenseinforepo;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $commondbentityManager, LicenseInfoRepository $licenseinforepo)
    {
        $this->commondbentityManager = $commondbentityManager;
        $this->licenseinforepo       =$licenseinforepo;
    }

    /**
     * @return EntityManager
     */
    public function getCommonDbEntityManager()
    {
        return $this->commondbentityManager;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->getCommonDbEntityManager()->getConnection();
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

    public function getAllPrepaidPlans()
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $qb->select('pp.*')
            ->from(MAUTIC_TABLE_PREFIX.'prepaidplans', 'pp');
        $qb->orderBy('pp.planorder', 'ASC');

        return $qb->execute()->fetchAll();
    }

    public function updateEmailCredits($plankey)
    {
        $qb = $this->getConnection()->createQueryBuilder();
        $qb->select('pp.*')
            ->from(MAUTIC_TABLE_PREFIX.'prepaidplans', 'pp');
        $qb->andWhere('pp.name = :name')
            ->setParameter('name', $plankey);
        $plans=$qb->execute()->fetchAll();
        if (sizeof($plans) > 0) {
            $plan           =$plans[0];
            $credits        =$plan['credits'];
            $months         =$plan['months'];
            $licentity      =$this->licenseinforepo->findAll()[0];
            $totalemailcount=$licentity->getTotalEmailCount();
            if (is_numeric($totalemailcount)) {
                $validity       =date('Y-m-d', strtotime("+$months months"));
                $totalemailcount=$totalemailcount + $credits;
                $licentity->setTotalEmailCount($totalemailcount);
                $licentity->setEmailValidity($validity);
                $this->licenseinforepo->saveEntity($licentity);
            }
        }
    }
}
