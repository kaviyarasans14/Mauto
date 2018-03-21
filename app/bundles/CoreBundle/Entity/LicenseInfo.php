<?php
/**
 * Created by PhpStorm.
 * User: cratio
 * Date: 24/2/18
 * Time: 12:42 PM.
 */

namespace Mautic\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

/**
 * Class LicenseInfo.
 */
class LicenseInfo
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $totalrecordcount;

    /**
     * @var string
     */
    private $actualrecordcount;

    /**
     * @var string
     */
    private $totalemailcount;

    /**
     * @var string
     */
    private $actualemailcount;

    /**
     * @var string
     */
    private $totalusercount;

    /**
     * @var string
     */
    private $activeusercount;

    /**
     * @var string
     */
    private $licenseddays;

    /**
     * @var string
     */
    private $licensestartdate;

    /**
     * @var string
     */
    private $licenseenddate;

    /**
     * @var string
     */
    private $totalattachementsize;

    /**
     * @var string
     */
    private $actualattachementsize;

    /**
     * @var string
     */
    private $bouncecount;

    /**
     * @var string
     */
    private $spamcount;

    /**
     * @var string
     */
    private $appstatus;

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('licenseinfo')
            ->setCustomRepositoryClass('Mautic\CoreBundle\Entity\LicenseInfoRepository');

        $builder->createField('id', 'integer')
            ->makePrimaryKey()
            ->generatedValue()
            ->build();

        $builder->createField('totalrecordcount', 'string')
            ->columnName('total_record_count')
            ->nullable()
            ->build();

        $builder->createField('actualrecordcount', 'string')
            ->columnName('actual_record_count')
            ->nullable()
            ->build();

        $builder->createField('totalemailcount', 'string')
            ->columnName('total_email_count')
            ->nullable()
            ->build();

        $builder->createField('actualemailcount', 'string')
            ->columnName('actual_email_count')
            ->nullable()
            ->build();

        $builder->createField('activeusercount', 'string')
            ->columnName('active_user_count')
            ->nullable()
            ->build();

        $builder->createField('totalusercount', 'string')
            ->columnName('total_user_count')
            ->nullable()
            ->build();
        $builder->createField('licenseddays', 'string')
            ->columnName('licensed_days')
            ->nullable()
            ->build();

        $builder->createField('licensestartdate', 'string')
            ->columnName('license_start_date')
            ->nullable()
            ->build();

        $builder->createField('licenseenddate', 'string')
            ->columnName('license_end_date')
            ->nullable()
            ->build();

        $builder->createField('totalattachementsize', 'string')
            ->columnName('total_attachement_size')
            ->nullable()
            ->build();

        $builder->createField('actualattachementsize', 'string')
            ->columnName('actual_attachement_size')
            ->nullable()
            ->build();

        $builder->createField('bouncecount', 'string')
            ->columnName('bounce_count')
            ->nullable()
            ->build();

        $builder->createField('spamcount', 'string')
            ->columnName('spam_count')
            ->nullable()
            ->build();

        $builder->createField('appstatus', 'string')
            ->columnName('app_status')
            ->nullable()
            ->build();
    }

    /**
     * Set id.
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
    public function getTotalRecordCount()
    {
        return $this->totalrecordcount;
    }

    /**
     * @param mixed $totalrecordcount
     */
    public function setTotalRecordCount($totalrecordcount)
    {
        $this->totalrecordcount = $totalrecordcount;
    }

    /**
     * @return mixed
     */
    public function getActualRecordCount()
    {
        return $this->actualrecordcount;
    }

    /**
     * @param mixed $actualrecordcount
     */
    public function setActualRecordCount($actualrecordcount)
    {
        $this->actualrecordcount = $actualrecordcount;
    }

    /**
     * @return mixed
     */
    public function getTotalEmailCount()
    {
        return $this->totalemailcount;
    }

    /**
     * @param mixed $totalemailcount
     */
    public function setTotalEmailCount($totalemailcount)
    {
        $this->totalemailcount = $totalemailcount;
    }

    /**
     * @return mixed
     */
    public function getActualEmailCount()
    {
        return $this->actualemailcount;
    }

    /**
     * @param mixed $actualemailcount
     */
    public function setActualEmailCount($actualemailcount)
    {
        $this->actualemailcount = $actualemailcount;
    }

    /**
     * @return mixed
     */
    public function getTotalUserCount()
    {
        return $this->totalusercount;
    }

    /**
     * @param mixed $totalusercount
     */
    public function setTotalUserCount($totalusercount)
    {
        $this->totalusercount = $totalusercount;
    }

    /**
     * @return mixed
     */
    public function getActiveUserCount()
    {
        return $this->activeusercount;
    }

    /**
     * @param mixed $activeusercount
     */
    public function setActiveUserCount($activeusercount)
    {
        $this->activeusercount = $activeusercount;
    }

    /**
     * @return mixed
     */
    public function getLicensedDays()
    {
        return $this->licenseddays;
    }

    /**
     * @param mixed $licenseddays
     */
    public function setLicensedDays($licenseddays)
    {
        $this->licenseddays = $licenseddays;
    }

    /**
     * @return mixed
     */
    public function getLicenseStart()
    {
        return $this->licensestartdate;
    }

    /**
     * @param mixed $licensestartdate
     */
    public function setLicenseStart($licensestartdate)
    {
        $this->licensestartdate = $licensestartdate;
    }

    /**
     * @return mixed
     */
    public function getLicenseEnd()
    {
        return $this->licenseenddate;
    }

    /**
     * @param mixed $licenseenddate
     */
    public function setLicenseEnd($licenseenddate)
    {
        $this->licenseenddate = $licenseenddate;
    }

    /**
     * @return mixed
     */
    public function getTotalAttachementSize()
    {
        return $this->totalattachementsize;
    }

    /**
     * @param mixed $totalattachementsize
     */
    public function setTotalAttachementSize($totalattachementsize)
    {
        $this->totalattachementsize = $totalattachementsize;
    }

    /**
     * @return mixed
     */
    public function getActualAttachementSize()
    {
        return $this->actualattachementsize;
    }

    /**
     * @param mixed $actualattachementsize
     */
    public function setActualAttachementSize($actualattachementsize)
    {
        $this->actualattachementsize = $actualattachementsize;
    }

    /**
     * @return mixed
     */
    public function getBounceCount()
    {
        return $this->bouncecount;
    }

    /**
     * @param mixed $bouncecount
     */
    public function setBounceCount($bouncecount)
    {
        $this->bouncecount = $bouncecount;
    }

    /**
     * @return mixed
     */
    public function getSpamCount()
    {
        return $this->spamcount;
    }

    /**
     * @param mixed $spamcount
     */
    public function setSpamCount($spamcount)
    {
        $this->spamcount = $spamcount;
    }

    /**
     * @return mixed
     */
    public function getAppStatus()
    {
        return $this->appstatus;
    }

    /**
     * @param mixed $appstatus
     */
    public function setAppStatus($appstatus)
    {
        $this->appstatus = $appstatus;
    }
}
