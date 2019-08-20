<?php
/**
 * Created by PhpStorm.
 * User: cratio
 * Date: 23/5/18
 * Time: 12:27 PM.
 */

namespace Mautic\EmailBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

class AwsVerifiedEmailsRepository extends CommonRepository
{
    public function saveEntity($entity, $flush = true)
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush($entity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTableAlias()
    {
        return 'a';
    }
}
