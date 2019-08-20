<?php
/**
 * Created by PhpStorm.
 * User: cratio
 * Date: 23/5/18
 * Time: 11:57 AM.
 */

namespace Mautic\EmailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class AwsConfig
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $bouncearnvalue;

    /**
     * @var string
     */
    private $complaintarnvalue;

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('awsconfig')
            ->setCustomRepositoryClass('Mautic\EmailBundle\Entity\AwsConfigRepository');

        $builder->createField('id', 'integer')
            ->makePrimaryKey()
            ->generatedValue()
            ->build();

        $builder->createField('bouncearnvalue', 'string')
            ->columnName('bounce_arn_value')
            ->nullable()
            ->build();

        $builder->createField('complaintarnvalue', 'string')
            ->columnName('complaint_arn_value')
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
    public function getBounceArnValue()
    {
        return $this->bouncearnvalue;
    }

    /**
     * @param mixed $bounceArnValue
     */
    public function setBounceArnValue($bouncearnvalue)
    {
        $this->bouncearnvalue = $bouncearnvalue;
    }

    /**
     * @return mixed
     */
    public function getComplaintArnValue()
    {
        return $this->complaintarnvalue;
    }

    /**
     * @param mixed $complaintArnValue
     */
    public function setComplaintArnValue($complaintarnvalue)
    {
        $this->complaintarnvalue = $complaintarnvalue;
    }
}
