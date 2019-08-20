<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;

/**
 * Class KYC.
 */
class KYC extends FormEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $industry;

    /**
     * @var string
     */
    private $usercount;

    /**
     * @var string
     */
    private $yearsactive;

    /**
     * @var string
     */
    private $subscribercount;

    /**
     * @var string
     */
    private $subscribersource;

    /**
     * @var string
     */
    private $emailcontent;

    /**
     * @var string
     */
    private $previoussoftware;

    /**
     * @var string
     */
    private $knowus;

    /**
     * @var string
     */
    private $others;

    /**
     * @var
     */
    private $conditionsagree = 0;

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('kyc')
                ->setCustomRepositoryClass('Mautic\SubscriptionBundle\Entity\KYCRepository');

        $builder->createField('id', 'integer')
            ->isPrimaryKey()
            ->generatedValue()
            ->build();

        $builder->createField('industry', 'string')
            ->columnName('industry')
            ->nullable()
            ->build();

        $builder->createField('usercount', 'string')
            ->columnName('usercount')
            ->nullable()
            ->build();

        $builder->createField('yearsactive', 'string')
            ->columnName('yearsactive')
            ->nullable()
            ->build();

        $builder->createField('subscribercount', 'string')
            ->columnName('subscribercount')
            ->nullable()
            ->build();

        $builder->createField('subscribersource', 'string')
            ->columnName('subscribersource')
            ->nullable()
            ->build();

        $builder->createField('emailcontent', 'string')
            ->columnName('emailcontent')
            ->nullable()
            ->build();

        $builder->createField('previoussoftware', 'string')
            ->columnName('previoussoftware')
            ->nullable()
            ->build();

        $builder->createField('knowus', 'string')
            ->columnName('knowus')
            ->nullable()
            ->build();

        $builder->createField('others', 'string')
            ->columnName('others')
            ->nullable()
            ->build();

        $builder->createField('conditionsagree', 'integer')
            ->columnName('conditionsagree')
            ->nullable()
            ->build();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * @param string $industry
     *
     * @return KYC
     */
    public function setIndustry($industry)
    {
        $this->industry = $industry;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsercount()
    {
        return $this->usercount;
    }

    /**
     * @param string $usercount
     *
     * @return KYC
     */
    public function setUsercount($usercount)
    {
        $this->usercount = $usercount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getYearsactive()
    {
        return $this->yearsactive;
    }

    /**
     * @param mixed $yearsactive
     *
     * @return KYC
     */
    public function setYearsactive($yearsactive)
    {
        $this->yearsactive = $yearsactive;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubscribercount()
    {
        return $this->subscribercount;
    }

    /**
     * @param mixed $subscribercount
     *
     * @return KYC
     */
    public function setSubscribercount($subscribercount)
    {
        $this->subscribercount = $subscribercount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubscribersource()
    {
        return $this->subscribersource;
    }

    /**
     * @param mixed $subscribersource
     *
     * @return KYC
     */
    public function setSubscribersource($subscribersource)
    {
        $this->subscribersource = $subscribersource;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmailcontent()
    {
        return $this->emailcontent;
    }

    /**
     * @param string $emailcontent
     *
     * @return KYC
     */
    public function setEmailcontent($emailcontent)
    {
        $this->emailcontent = $emailcontent;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrevioussoftware()
    {
        return $this->previoussoftware;
    }

    /**
     * @param mixed $previoussoftware
     *
     * @return KYC
     */
    public function setPrevioussoftware($previoussoftware)
    {
        $this->previoussoftware = $previoussoftware;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getKnowus()
    {
        return $this->knowus;
    }

    /**
     * @param mixed $knowus
     *
     * @return KYC
     */
    public function setKnowus($knowus)
    {
        $this->knowus = $knowus;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOthers()
    {
        return $this->others;
    }

    /**
     * @param mixed $others
     *
     * @return KYC
     */
    public function setOthers($others)
    {
        $this->others = $others;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConditionsagree()
    {
        return $this->conditionsagree;
    }

    /**
     * @param mixed $conditionsagree
     *
     * @return KYC
     */
    public function setConditionsagree($conditionsagree)
    {
        $this->conditionsagree = $conditionsagree;

        return $this;
    }
}
