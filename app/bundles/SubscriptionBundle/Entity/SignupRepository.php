<?php

namespace Mautic\SubscriptionBundle\Entity;

use Doctrine\ORM\EntityManager;

class SignupRepository
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

    public function checkisRecordAvailable($emailid)
    {
        $qb = $this->getConnection()->createQueryBuilder();

        $qb->select('l.id')
            ->from(MAUTIC_TABLE_PREFIX.'leads', 'l');
        $qb->andWhere('l.email = :email')
            ->setParameter('email', $emailid);
        $leads = $qb->execute()->fetchAll();

        if (!empty($leads)) {
            return $leads[0]['id'];
        } else {
            return false;
        }
    }

    public function updateSignupInfo($accountData, $billingData, $userData)
    {
        $qb = $this->getConnection()->createQueryBuilder();

        $firstName      = $userData['firstName'];
        $lastName       = $userData['lastName'];
        $email          = $userData['email'];

        $companyname    = $billingData['companyname'];
        $companyaddress = $billingData['companyaddress'];
        $postalcode     = $billingData['postalcode'];
        $state          = $billingData['state'];
        $city           = $billingData['city'];
        $country        = $billingData['country'];
        $gstnumber      = $billingData['gstnumber'];

        $phonenumber    = $accountData['phonenumber'];
        $timezone       = $accountData['timezone'];

        $recordid = $this->checkisRecordAvailable($email);
        if (!$recordid) {
        } else {
            $qb->update(MAUTIC_TABLE_PREFIX.'leads')
                ->set('address1', ':address')
                ->set('city', ':city')
                ->set('state', ':state')
                ->set('zipcode', ':zipcode')
                ->set('timezone', ':timezone')
                ->set('country', ':country')
                ->set('gst_no', ':gst_no')
                ->setParameter('address', $companyaddress)
                ->setParameter('city', $city)
                ->setParameter('state', $state)
                ->setParameter('zipcode', $postalcode)
                ->setParameter('timezone', $timezone)
                ->setParameter('country', $country)
                ->setParameter('gst_no', $gstnumber)
                ->where(
                    $qb->expr()->in('id', $recordid)
                )
                ->execute();
        }
    }

    public function updateKYCInfo($kycdata, $userdata)
    {
        $qb    = $this->getConnection()->createQueryBuilder();
        $email = $userdata['email'];

        $industry           = $kycdata['industry'];
        $usercount          = $kycdata['usercount'];
        $yearsactive        = $kycdata['yearsactive'];
        $subscribercount    = $kycdata['subscribercount'];
        $subscribersource   = $kycdata['subscribersource'];
        $emailcontent       = $kycdata['emailcontent'];
        $previoussoftware   = $kycdata['previoussoftware'];
        $knowus             = $kycdata['knowus'];
        $others             = $kycdata['others'];

        $recordid = $this->checkisRecordAvailable($email);
        if (!$recordid) {
        } else {
            $qb->update(MAUTIC_TABLE_PREFIX.'leads')
                ->set('what_industry_are_you_in', ':industry')
                ->set('how_many_people_work_for', ':usercount')
                ->set('how_old_is_your_organizat', ':yearsactive')
                ->set('how_many_subscribers_do_y', ':subscribercount')
                ->set('what_is_your_marketing_go', ':subscribersource')
                ->set('have_you_used_other_email', ':previoussoftware')
                ->set('other_marketing_software', ':emailcontent')
                ->set('how_did_you_find_out_abou', ':knowus')
                ->set('other', ':others')
                ->setParameter('industry', $industry)
                ->setParameter('usercount', $usercount)
                ->setParameter('yearsactive', $yearsactive)
                ->setParameter('subscribercount', $subscribercount)
                ->setParameter('subscribersource', $subscribersource)
                ->setParameter('previoussoftware', $previoussoftware)
                ->setParameter('emailcontent', $emailcontent)
                ->setParameter('knowus', $knowus)
                ->setParameter('others', $others)
                ->where(
                    $qb->expr()->in('id', $recordid)
                )
                ->execute();
        }
    }
}
