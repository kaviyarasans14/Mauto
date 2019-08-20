<?php
/**
 * Created by PhpStorm.
 * User: cratio
 * Date: 23/5/18
 * Time: 12:00 PM.
 */

namespace Mautic\EmailBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

class AwsConfigRepository extends CommonRepository
{
    public function saveEntity($entity, $flush = true)
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush($entity);
        }
    }
}
