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

/**
 * Class PaymentHistory.
 */
class PaymentHistory
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $orderid;

    /**
     * @var string
     */
    private $paymentid;

    /**
     * @var string
     */
    private $paymentstatus;

    /**
     * @var string
     */
    private $provider;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var string
     */
    private $beforecredits;

    /**
     * @var string
     */
    private $addedcredits;

    /**
     * @var string
     */
    private $aftercredits;

    /**
     * @var string
     */
    private $validitytill;

    /**
     * @var string
     */
    private $planname;

    /**
     * @var string
     */
    private $planlabel;

    /**
     * @var int
     */
    private $createdBy;

    /**
     * @var string
     */
    private $createdByUser;

    /**
     * @var null|\DateTime
     */
    private $createdOn;

    /**
     * @var string
     */
    private $netamount;

    /**
     * @var string
     */
    private $taxamount;

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('paymenthistory')
                ->setCustomRepositoryClass('Mautic\SubscriptionBundle\Entity\PaymentRepository');

        $builder->createField('id', 'integer')
            ->isPrimaryKey()
            ->generatedValue()
            ->build();

        $builder->createField('orderid', 'string')
            ->columnName('orderid')
            ->nullable()
            ->build();

        $builder->createField('paymentid', 'string')
            ->columnName('paymentid')
            ->nullable()
            ->build();

        $builder->createField('paymentstatus', 'string')
            ->columnName('paymentstatus')
            ->nullable()
            ->build();

        $builder->createField('provider', 'string')
            ->columnName('provider')
            ->nullable()
            ->build();

        $builder->createField('currency', 'string')
            ->columnName('currency')
            ->nullable()
            ->build();

        $builder->createField('amount', 'string')
            ->columnName('amount')
            ->nullable()
            ->build();

        $builder->createField('beforecredits', 'string')
            ->columnName('beforecredits')
            ->nullable()
            ->build();

        $builder->createField('addedcredits', 'string')
            ->columnName('addedcredits')
            ->nullable()
            ->build();

        $builder->createField('aftercredits', 'string')
            ->columnName('aftercredits')
            ->nullable()
            ->build();

        $builder->createField('validitytill', 'string')
            ->columnName('validitytill')
            ->nullable()
            ->build();
        $builder->createField('planname', 'string')
            ->columnName('planname')
            ->nullable()
            ->build();
        $builder->createField('planlabel', 'string')
            ->columnName('planlabel')
            ->nullable()
            ->build();
        $builder->createField('createdBy', 'integer')
            ->columnName('createdBy')
            ->nullable()
            ->build();
        $builder->createField('createdByUser', 'string')
            ->columnName('createdByUser')
            ->nullable()
            ->build();
        $builder->createField('createdOn', 'datetime')
            ->columnName('createdOn')
            ->nullable()
            ->build();
        $builder->createField('netamount', 'string')
            ->columnName('netamount')
            ->nullable()
            ->build();
        $builder->createField('taxamount', 'string')
            ->columnName('taxamount')
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
    public function getOrderID()
    {
        return $this->orderid;
    }

    /**
     * @param string $orderid
     *
     * @return PaymentHistory
     */
    public function setOrderID($orderid)
    {
        $this->orderid = $orderid;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentID()
    {
        return $this->paymentid;
    }

    /**
     * @param string $paymentid
     *
     * @return PaymentHistory
     */
    public function setPaymentID($paymentid)
    {
        $this->paymentid = $paymentid;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentStatus()
    {
        return $this->paymentstatus;
    }

    /**
     * @param string $status
     *
     * @return PaymentHistory
     */
    public function setPaymentStatus($status)
    {
        $this->paymentstatus = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     *
     * @return PaymentHistory
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return PaymentHistory
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     *
     * @return PaymentHistory
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBeforeCredits()
    {
        return $this->beforecredits;
    }

    /**
     * @param string $credits
     *
     * @return PaymentHistory
     */
    public function setBeforeCredits($credits)
    {
        $this->beforecredits = $credits;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddedCredits()
    {
        return $this->addedcredits;
    }

    /**
     * @param string $credits
     *
     * @return PaymentHistory
     */
    public function setAddedCredits($credits)
    {
        $this->addedcredits = $credits;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAfterCredits()
    {
        return $this->aftercredits;
    }

    /**
     * @param string $credits
     *
     * @return PaymentHistory
     */
    public function setAfterCredits($credits)
    {
        $this->aftercredits = $credits;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidityTill()
    {
        return $this->validitytill;
    }

    /**
     * @param string $validity
     *
     * @return PaymentHistory
     */
    public function setValidityTill($validity)
    {
        $this->validitytill = $validity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlanName()
    {
        return $this->planname;
    }

    /**
     * @param string $name
     *
     * @return PaymentHistory
     */
    public function setPlanName($name)
    {
        $this->planname = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlanLabel()
    {
        return $this->planlabel;
    }

    /**
     * @param string $label
     *
     * @return PaymentHistory
     */
    public function setPlanLabel($label)
    {
        $this->planlabel = $label;

        return $this;
    }

    /**
     * @param datetime $createdOn
     *
     * @return PaymentHistory
     */
    public function setcreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getcreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param string $createdByUser
     *
     * @return PaymentHistory
     */
    public function setcreatedByUser($createdByUser)
    {
        $this->createdByUser = $createdByUser;

        return $this;
    }

    /**
     * @param int $createdby
     *
     * @return PaymentHistory
     */
    public function setcreatedBy($createdby)
    {
        $this->createdBy = $createdby;

        return $this;
    }

    /**
     * @param string $netamount
     *
     * @return PaymentHistory
     */
    public function setNetamount($netamount)
    {
        $this->netamount = $netamount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNetamount()
    {
        return $this->netamount;
    }

    /**
     * @param string $taxamount
     *
     * @return PaymentHistory
     */
    public function setTaxamount($taxamount)
    {
        $this->taxamount = $taxamount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaxamount()
    {
        return $this->taxamount;
    }
}
