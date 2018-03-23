<?php
/**
 * Created by PhpStorm.
 * User: cratio
 * Date: 26/2/18
 * Time: 11:35 AM.
 */

namespace Mautic\CoreBundle\Helper;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Entity\LicenseInfo;
use Mautic\CoreBundle\Entity\LicenseInfoRepository;

class LicenseInfoHelper
{
    private $em;

    /**
     * @var LicenseInfoRepository
     */
    private $licenseinfo;

    /**
     * LicenseInfoHelper constructor.
     *
     * @param EntityManager         $entityManager
     * @param LicenseInfoRepository $licenseinforepository
     */
    public function __construct(EntityManager $entityManager, LicenseInfoRepository $licenseinforepository)
    {
        $this->em         = $entityManager;
        $this->licenseinfo=$licenseinforepository;
    }

    public function intRecordCount($totalRecordCount, $isSum)
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        if (!isset($totalRecordCount)) {
            $totalRecordCount = 0;
        }
        $previousValue=$entity->getActualRecordCount();
        if ($isSum) {
            $totalCountValue = $previousValue + $totalRecordCount;
        } else {
            $totalCountValue = $previousValue - $totalRecordCount;
        }

        $entity->setActualRecordCount($totalCountValue);

        $this->licenseinfo->saveEntity($entity);
    }

    public function intUserCount($totalUserCount, $isSum)
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        if (!isset($totalUserCount)) {
            $totalUserCount = 0;
        }

        $previousValue=$entity->getActiveUserCount();
        if ($isSum) {
            $totalUser = $previousValue + $totalUserCount;
        } else {
            $totalUser = $previousValue - $totalUserCount;
        }
        $entity->setActiveUserCount($totalUser);

        $this->licenseinfo->saveEntity($entity);
    }

    public function intAttachmentSize($attachmentSize, $isSum)
    {
        $data = $this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }

        if (!isset($attachmentSize)) {
            $attachmentSize = 0;
        }
        if (strpos(($attachmentSize), 'KB') !== false) {
            $sizeInBytes = $attachmentSize * 1024;
        } elseif (strpos(($attachmentSize), 'MB') !== false) {
            $sizeInBytes = $attachmentSize * 1048576;
        } else {
            $sizeInBytes = $attachmentSize;
        }

        $previousValue = $entity->getActualAttachementSize() * 1000000;

        if ($isSum) {
            $totalSize = $previousValue + $sizeInBytes;
        } else {
            $totalSize = $previousValue - $sizeInBytes;
        }

        $updateSizeInMb = number_format($totalSize / 1000000, 2);

        $entity->setActualAttachementSize($updateSizeInMb);

        $this->licenseinfo->saveEntity($entity);
    }

    public function intEmailCount($emailCount)
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        if (!isset($emailCount)) {
            $emailCount = 0;
        }

        $previousValue= $entity->getActualEmailCount();
        $totalSize    = $previousValue + $emailCount;
        $entity->setActualEmailCount($totalSize);

        $this->licenseinfo->saveEntity($entity);
    }

    public function intBounceCount($bounceCount)
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        if (!isset($bounceCount)) {
            $bounceCount = 0;
        }

        $previousValue= $entity->getBounceCount();
        $totalSize    = $previousValue + $bounceCount;
        $entity->setBounceCount($totalSize);

        $this->licenseinfo->saveEntity($entity);
    }

    public function getTotalRecordCount()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }

        $totalRecordCount= $entity->getTotalRecordCount();

        return $totalRecordCount;
    }

    public function isValidRecordAdd()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }

        $totalRecordCount  = $entity->getTotalRecordCount();
        $actualRecordCount = $entity->getActualRecordCount();

        if ($totalRecordCount == 'UL') {
            return true;
        } else {
            if ($totalRecordCount > $actualRecordCount) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getActualRecordCount()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $actualRecordCount= $entity->getActualRecordCount();

        return $actualRecordCount;
    }

    public function getTotalUserCount()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $actualRecordCount= $entity->getTotalUserCount();

        return $actualRecordCount;
    }

    public function getActiveUserCount()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $actualRecordCount= $entity->getActiveUserCount();

        return $actualRecordCount;
    }

    public function isValidUserCount()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }

        $totalUserCOunt  = $entity->getTotalUserCount();
        $actualUserCount = $entity->getActiveUserCount();

        if ($totalUserCOunt == 'UL') {
            return true;
        } else {
            if ($totalUserCOunt > $actualUserCount) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getLicensedDays()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $licensedDays= $entity->getLicensedDays();

        return $licensedDays;
    }

    public function getLicenseRemainingDays()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $licenseStart  = $entity->getLicenseStart();
        $licenseEnd    = $entity->getLicenseEnd();
        $licenseRemDays= $entity->getLicensedDays();

        if ($licenseRemDays == 'UL') {
            $licenseremdays = 7300;
        } else {
            $licenseremdays = round((strtotime($licenseEnd) - strtotime($licenseStart)) / 86400);
        }

        return $licenseremdays;
    }

    public function getTotalAttachementSize()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $totalAttachmentSize= $entity->getTotalAttachementSize();

        return $totalAttachmentSize;
    }

    public function getActualAttachementSize()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $actualAttachmentSize= $entity->getActualAttachementSize();

        return $actualAttachmentSize;
    }

    public function getActualEmailCount()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $actualEmailCount= $entity->getActualEmailCount();

        return $actualEmailCount;
    }

    public function getTotalEmailCount()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $totalEmailCount= $entity->getTotalEmailCount();

        return $totalEmailCount;
    }

    public function isValidEmailCount()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }

        $totalEmailCount  = $entity->getTotalEmailCount();
        $actualEmailCount = $entity->getActualEmailCount();

        if ($totalEmailCount == 'UL') {
            return true;
        } else {
            if ($totalEmailCount > $actualEmailCount) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getTotalEmailUsage()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $actualEmailCount = $entity->getActualEmailCount();
        $totalEmailCount  = $entity->getTotalEmailCount();

        if ($totalEmailCount == 'UL') {
            return $totalEmailCount;
        } else {
            if ($actualEmailCount > 0) {
                $emailUsageCount = ($actualEmailCount / $totalEmailCount) * 100;

                return $emailUsageCount;
            }
        }
    }

    public function getAppStatus()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }

        $appStatus  = $entity->getAppStatus();

        return $appStatus;
    }

    public function isHavingEmailValidity()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $currentDate     = date('Y-m-d');
        $emailValidity   = $entity->getEmailValidity();
        $totalEmailCount = $entity->getTotalEmailCount();

        $remDays = round((strtotime($emailValidity) - strtotime($currentDate)) / 86400);

        if ($totalEmailCount == 'UL') {
            return true;
        } else {
            if ($remDays > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getEmailValidityDays()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }
        $currentDate     = date('Y-m-d');
        $emailValidity   = $entity->getEmailValidity();
        $totalEmailCount = $entity->getTotalEmailCount();

        $validityDays = round((strtotime($emailValidity) - strtotime($currentDate)) / 86400);

        if ($totalEmailCount == 'UL') {
            return true;
        } else {
            if ($validityDays > 0) {
                return $validityDays;
            } else {
                return false;
            }
        }
    }

    public function getEmailBounceUsageCount()
    {
        $data=$this->em->getRepository('Mautic\CoreBundle\Entity\LicenseInfo')->findAll();

        if (sizeof($data) > 0 && $data != null) {
            $entity = $data[0];
        }
        if (!$data) {
            $entity = new LicenseInfo();
        }

        $actualEmailCount  = $entity->getActualEmailCount();
        $bouncedEmailCount = $entity->getBounceCount();

        if ($actualEmailCount > 0) {
            $bounceUsageCount = ($bouncedEmailCount / $actualEmailCount) * 100;

            return $bounceUsageCount;
        }
    }
}
