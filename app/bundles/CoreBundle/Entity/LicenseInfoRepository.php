<?php
/**
 * Created by PhpStorm.
 * User: cratio
 * Date: 24/2/18
 * Time: 1:40 PM.
 */

namespace Mautic\CoreBundle\Entity;

class LicenseInfoRepository extends CommonRepository
{
    public function saveEntity($entity, $flush = true)
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush($entity);
        }
    }
}
