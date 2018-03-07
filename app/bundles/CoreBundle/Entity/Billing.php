<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

/**
 * Class Billing.
 */
class Billing extends FormEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $companyname = '';

    /**
     * @var string
     */
    private $companyaddress = '';

    /**
     * @var string
     */
    private $accountingemail = '';

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('billinginfo')
                ->setCustomRepositoryClass('Mautic\CoreBundle\Entity\BillingRepository');

        $builder->createField('id', 'integer')
            ->isPrimaryKey()
            ->generatedValue()
            ->build();

        $builder->createField('companyname', 'string')
            ->columnName('companyname')
            ->nullable()
            ->build();

        $builder->createField('companyaddress', 'string')
            ->columnName('companyaddress')
            ->nullable()
            ->build();

        $builder->createField('accountingemail', 'string')
            ->columnName('accountingemail')
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
    public function getCompanyname()
    {
        return $this->companyname;
    }

    /**
     * @param string $accountname
     *
     * @return Account
     */
    public function setCompanyname($accountname)
    {
        $this->companyname = $accountname;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyaddress()
    {
        return $this->companyaddress;
    }

    /**
     * @param string $companyaddress
     *
     * @return Account
     */
    public function setCompanyaddress($companyaddress)
    {
        $this->companyaddress = $companyaddress;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccountingemail()
    {
        return $this->accountingemail;
    }

    /**
     * @param mixed $email
     *
     * @return Account
     */
    public function setAccountingemail($email)
    {
        $this->accountingemail = $email;

        return $this;
    }
}
