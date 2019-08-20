<?php
/**
 * Created by PhpStorm.
 * User: cratio
 * Date: 23/5/18
 * Time: 12:25 PM.
 */

namespace Mautic\EmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class AwsVerifiedEmails
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $verifiedemails;

    /**
     * @var string
     */
    private $verificationstatus;

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('awsverifiedemails')
            ->setCustomRepositoryClass('Mautic\EmailBundle\Entity\AwsConfigRepository');

        $builder->createField('id', 'integer')
            ->makePrimaryKey()
            ->generatedValue()
            ->build();

        $builder->createField('verifiedemails', 'string')
            ->columnName('verified_emails')
            ->nullable()
            ->build();

        $builder->createField('verificationstatus', 'string')
            ->columnName('verification_status')
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
    public function getVerifiedEmails()
    {
        return $this->verifiedemails;
    }

    /**
     * @param mixed $verifiedemails
     */
    public function setVerifiedEmails($verifiedemails)
    {
        $this->verifiedemails = $verifiedemails;
    }

    /**
     * @return mixed
     */
    public function getVerificationStatus()
    {
        return $this->verificationstatus;
    }

    /**
     * @param mixed $verificationstatus
     */
    public function setVerificationStatus($verificationstatus)
    {
        $this->verificationstatus = $verificationstatus;
    }
}
